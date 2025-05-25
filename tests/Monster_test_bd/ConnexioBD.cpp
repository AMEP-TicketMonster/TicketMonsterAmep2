#include "ConnexioBD.h"
#include <fstream>
#include <iostream>
#include <string>
using namespace std;

ConnexioBD::ConnexioBD() {
    try {
        sql::mysql::MySQL_Driver* driver = NULL;
        driver = sql::mysql::get_mysql_driver_instance();
        ifstream fitxer_configuracio("config_connexio.ini");
        if (fitxer_configuracio){
            string host_name;
            string user;
            string password;
            getline(fitxer_configuracio, host_name);
            getline(fitxer_configuracio, user);
            getline(fitxer_configuracio, password);
            con_ = driver->connect(host_name, user, password);
            con_->setSchema("amep06");
        }
        else {
            std::cout << "Error: no s'ha pogut llegir el fitxer configuració" << endl;
        }
    }
    catch (sql::SQLException& e) {
        std::cerr << "Error: " << e.what() << std::endl;
    }
}

ConnexioBD::~ConnexioBD() {
    con_->close();
}

sql::ResultSet* ConnexioBD::consulta(const std::string& query) const {
    sql::Statement* stmt = NULL;
    stmt = con_->createStatement();
    return stmt->executeQuery(query);
}

void ConnexioBD::executa(const std::string& operacio) {
    sql::Statement* stmt = NULL;
    stmt = con_->createStatement();
    stmt->execute(operacio);
}