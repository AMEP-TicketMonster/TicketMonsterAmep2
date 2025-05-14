#pragma once
#include <cppconn/driver.h>
#include <cppconn/exception.h>
#include <cppconn/statement.h>
#include <mysql_connection.h>
#include <mysql_driver.h>

class ConnexioBD
{
public:
    ConnexioBD();
    ~ConnexioBD();

    sql::ResultSet* consulta(const std::string& query) const;
    void executa(const std::string& operacio);

private:
    sql::Connection* con_ = NULL;
};

