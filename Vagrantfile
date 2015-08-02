# -*- mode: ruby -*-
# vi: set ft=ruby :
require 'yaml'

# Variables 
data = YAML.load_file("provisioning/variables.yml")

Vagrant.require_version ">= 1.6.0"

Vagrant.configure("2") do |config|
  # All Vagrant configuration is done below. see vagrantup.com.
  config.vm.box = "#{data['vm']['box']}"
  config.vm.box_url = "#{data['vm']['box_url']}"

  # configure network, ports
  config.vm.network "private_network", ip: "#{data['vm']['ip_address']}"
  #config.vm.hostname = "#{data['vm']['hostname']}" #commenting out until there is a fix
  config.hostsupdater.aliases = ["#{data['vm']['alias1']}","#{data['vm']['alias2']}"]

  # sync folders
  config.vm.synced_folder "#{data['vm']['sync_host']}", "#{data['vm']['sync_vm']}", 
    group: 'www-data', owner: 'www-data'

  # Provisioning configuration for Ansible
  config.vm.provision "ansible" do |ansible|
    ansible.playbook = "provisioning/playbook.yml"
    ansible.inventory_path = "provisioning/ansible_hosts" 
    ansible.host_key_checking = true
    ansible.sudo = true
    ansible.limit = 'all'
    ansible.extra_vars = { ansible_ssh_args: '-o ForwardAgent=yes'}
  end

end

