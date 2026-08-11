#!/usr/bin/env bash
set -euo pipefail

# Resolve one running container by its exact name. Docker's `name=` filter is
# substring-based, so `name=mariadb` can accidentally select several stacks.
# A caller may pass a compose service label as the second argument when the
# stack deliberately uses generated container names.
name=${1:-}
label=${2:-}

if [[ -n "$label" ]]; then
    mapfile -t ids < <(docker ps -q --filter "label=com.docker.compose.service=$label")
else
    [[ -n "$name" ]] || { echo 'container name is required' >&2; exit 2; }
    mapfile -t ids < <(docker ps -q --filter "name=^/${name}$")
fi

case "${#ids[@]}" in
    1) printf '%s\n' "${ids[0]}" ;;
    0)
        echo "No running Docker container matched ${label:+compose service label $label}${label:-exact name $name}." >&2
        exit 1
        ;;
    *)
        echo "Expected one Docker container, found ${#ids[@]} matching ${label:+compose service label $label}${label:-exact name $name}." >&2
        printf 'Matched IDs: %s\n' "${ids[*]}" >&2
        exit 1
        ;;
esac
