# Convert every remaining MyISAM table to InnoDB.
#
# The conversion itself runs in the upgrade_046_1_7_3() hook in Mdl_Setup rather
# than as ALTER statements here, because the set of tables to convert can only be
# known at runtime: two tables (ip_sessions, ip_login_log) were created with no
# ENGINE clause and so inherit whatever the server default was at install time,
# and an install that has already been converted by hand must not be rebuilt
# needlessly. The hook reads information_schema and touches only tables that
# actually exist and are actually MyISAM, so it is safe to re-run.
