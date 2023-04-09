
In order to run this project (in a Linux environment only) you need to :

- Link limalably_framework with a folder link named limalably_framework
- Rename the config/hostnames/ folders in order to be the real host names, both for your local and your remote hosts
- Create a simbolic link in config/hostnames/ linking your local hostname with your hostname (required to run migration script)
- Configure your webserver to point at the wwwroot folder
- Configure your project actually putting real database credentials in the config files
- Create the databases with the correct user permissions
- Run the migrations using ./bin/migrate.sh
- Configure your admin user in the config files too
- Run composer in the project root in order to fetch the required dependencies (it uses twig and some other libraries ...)

In order to deploy your project remotely and run your migrations remotely
you can use the deployer tool. Copy limalably_framework/tools/deployer.php on
your remote wwwroot and only that. Then use ./bin/deployer.sh to execute
all deploy phases. It will require you to issue some commands in order to do all the required work. The command has english help.

- Marco B.
