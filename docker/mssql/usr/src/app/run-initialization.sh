# Wait to be sure that SQL Server came up
sleep 90s

# Run the setup script to create the DB
/opt/mssql-tools/bin/sqlcmd -S localhost -U sa -P "${MSSQL_SA_PASSWORD:-notProvided}" -d master -i create-database.sql