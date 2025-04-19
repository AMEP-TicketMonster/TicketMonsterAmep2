#include "Dades.h"
#include <cppconn/driver.h>
#include <cppconn/exception.h>
#include <cppconn/statement.h>
#include <algorithm>
using namespace std;

// Pre: Res
// Post: Inicialitza un objecte Dades
Dades::Dades() {

}

// Pre: usuari conté dades vàlides per a un nou usuari
// Post: Registra un nou usuari a la base de dades
void Dades::crea_usuari(const Usuari& usuari) {
    try {
        Usuari dummy;
        if (!get_usuari(usuari.email, dummy)) {
            string sql = "INSERT INTO Usuaris (nom, cognom, email, contrasenya, saldo) VALUES ('"
                + usuari.nom
                + "','" + usuari.cognom
                + "','" + usuari.email
                + "','" + usuari.contrasenya
                + "','" + to_string(usuari.saldo) + "')";
            bd_.executa(sql);
        }
        else {
            cout << "Error: email existent" << endl;
        }
    }
    catch (sql::SQLException& e) {
        cerr << "SQL Error: " << e.what() << endl;
    }
}

int Dades::get_IdUsuari(const string& email, const string& contrasenya) {
    try {
        int ret = -1;
        string sql = "SELECT idUsuari "
            "FROM Usuaris WHERE email = '" + email + "' AND contrasenya = '" + contrasenya + "'";
        sql::ResultSet* res = bd_.consulta(sql);
        if (res->next()) {
            ret = res->getInt("idUsuari");
        }
        return ret;
    }
    catch (sql::SQLException& e) {
        cerr << "SQL Error: " << e.what() << endl;
        return false;
    }
}

float Dades::get_SaldoUsuari(const int id_usuari) {
    try {
        float ret = -1.0;
        string sql = "SELECT saldo "
            "FROM Usuaris WHERE idUsuari = '" + to_string(id_usuari) + "'";
        sql::ResultSet* res = bd_.consulta(sql);
        if (res->next()) {
            ret = res->getDouble("saldo");
        }
        return ret;
    }
    catch (sql::SQLException& e) {
        cerr << "SQL Error: " << e.what() << endl;
        return false;
    }
}

// PRE: cert
// POST: retorna true si nom existeix a la taula Usuaris i omple l´estructura usuari
bool Dades::get_usuari(const string& email, Usuari& usuari) {
    try {
        bool ret = false;
        string sql = "SELECT * "
                     "FROM Usuaris WHERE email = '" + email + "'";
        sql::ResultSet* res = bd_.consulta(sql);
        if (res->next()) {
            usuari.nom = res->getString("nom");
            usuari.cognom = res->getString("cognom");
            usuari.contrasenya = res->getString("contrasenya");
            usuari.email = res->getString("email");
            ret = true;
        }
        return ret;
    }
    catch (sql::SQLException& e) {
        cerr << "SQL Error: " << e.what() << endl;
        return false;
    }
}



bool Dades::get_sales(const std::string& ciutat, std::vector<Sala>& sales) {
    try {
        bool ret = false;
        string sql = "SELECT * "
                     "FROM Sales WHERE BINARY ciutat = '" + ciutat + "'";
        sql::ResultSet* res = bd_.consulta(sql);
        while (res->next()) {
            Sala sala;
            sala.nom = res->getString("nom");
            sala.ciutat = res->getString("ciutat");
            sala.capacitat = res->getString("capacitat");
            sales.push_back(sala);
            ret = true;
        }
        return ret;
    }
    catch (sql::SQLException& e) {
        cerr << "SQL Error: " << e.what() << endl;
        return false;
    }
}

// Pre: nom conté el nom d'un usuari existent
// Post: Elimina l'usuari de la base de dades
void Dades::esborra_usuari(const string& nom) {
    try {
        string sql = "DELETE FROM Usuaris WHERE nom = '" + nom + "';";
        bd_.executa(sql);
    }
    catch (sql::SQLException& e) {
        cerr << "SQL Error: " << e.what() << endl;
    }
}

void Dades::afegeix_disponibilitat_sala(const DisponibilitatSala& dispo) {
    try {
        int idSala = get_IDSala(dispo.nom_sala);
        int idDataSala = afegeix_dataSala(idSala, dispo.dia, dispo.hora_inici, dispo.hora_fi);

        string sql = "INSERT INTO DisponibilitatSales(idSala, idDataSala, idEstatSala)"
            "VALUES (" + to_string(idSala) + ", " + to_string(idDataSala) + ", " + to_string(EstatSala::Disponible) + ")";
        bd_.executa(sql);
    }
    catch (sql::SQLException& e) {
        cerr << "SQL Error: " << e.what() << endl;
    }
}

void Dades::afegeix_assaig(const Assaig& assaig) {
    int idGrupMusical = get_IDGrupMusical(assaig.nom_grup_musical);
    if (idGrupMusical != -1) {
        int idSala = get_IDSala(assaig.nom_sala);
        int entrades_disponibles = get_CapacitatSala(assaig.nom_sala);
        if (idSala != -1) {
            int idDataSala = get_IDDataSala(idSala, assaig.dia, assaig.hora_inici, assaig.hora_fi);
            if (idDataSala != -1) {
                bool salaDisponible = estaSalaDisponible(idSala, idDataSala);
                if (salaDisponible) {
                    do_afegeix_assaig(idGrupMusical, idSala, idDataSala, assaig.preu_entrada_public, entrades_disponibles);
                    modifica_disponibilitat(EstatSala::Reservada, idSala, idDataSala);
                }
            }
            else {
                cout << "error idDataSala no existeix " << endl;
            }
        }
        else {
            cout << "error sala " << assaig.nom_sala << " no existeix " << endl;
        }
    }
    else {
        cout << "error grup musical " << assaig.nom_grup_musical << " no existeix " << endl;
    }
}

// 
// Post: aquesta funció retorna el ID del registre afegit a DataSala. Si la entrada ja existeix no fa res
int Dades::afegeix_dataSala(int idSala, const string& dia, const string& hora_inici, const string& hora_fi) {
    int ret = -1;
    try {
        string sql = "INSERT INTO DataSala(idSala, dia, hora_inici, hora_fi)"
            "Values(" + to_string(idSala) + ", '" + dia + "', '" + hora_inici + "', '" + hora_fi + "')";
        string check_sql = "SELECT COUNT(*) FROM DataSala WHERE idSala = " + to_string(idSala) +
            " AND dia = '" + dia + "' AND hora_inici = '" + hora_inici + "' AND hora_fi = '" + hora_fi + "'";
        sql::ResultSet* res = bd_.consulta(check_sql);
        if (res->next() && res->getInt(1) == 0) {
            bd_.executa(sql);
            sql = "SELECT LAST_INSERT_ID()";
            sql::ResultSet* res = bd_.consulta(sql);
            if (res->next()) {
                ret = res->getInt("LAST_INSERT_ID()");
            }
        }
        else {
            cout << "Error: Duplicate entry detected!" << endl;
        }
    }
    catch (sql::SQLException& e) {
        cerr << "SQL Error: " << e.what() << endl;
    }
    return ret;
}

int Dades::get_IDSala(const string& nom) {
    int ret = -1;
    try {
        string sql = "SELECT idSala FROM Sales WHERE nom = '" + nom + "'";
        sql::ResultSet* res = bd_.consulta(sql);
        if (res->next()) {
            ret = res->getInt("idSala");
        }
    }
    catch (sql::SQLException& e) {
        cerr << "SQL Error: " << e.what() << endl;
    }
    return ret;
}


int Dades::get_CapacitatSala(const string& nom) {
    int ret = -1;
    try {
        string sql = "SELECT capacitat FROM Sales WHERE nom = '" + nom + "'";
        sql::ResultSet* res = bd_.consulta(sql);
        if (res->next()) {
            ret = res->getInt("capacitat");
        }
    }
    catch (sql::SQLException& e) {
        cerr << "SQL Error: " << e.what() << endl;
    }
    return ret;
}

std::string Dades::get_NomSala(const int id_sala) {
    string ret;
    try {
        string sql = "SELECT nom FROM Sales WHERE idSala = " + to_string(id_sala);
        sql::ResultSet* res = bd_.consulta(sql);
        if (res->next()) {
            ret = res->getString("nom");
        }
    }
    catch (sql::SQLException& e) {
        cerr << "SQL Error: " << e.what() << endl;
    }
    return ret;
}

int Dades::get_IDGrupMusical(const std::string& nom_grup) {
    int ret = -1;
    try {
        string sql = "SELECT idGrup FROM GrupsMusicals WHERE nomGrup = '" + nom_grup + "'";
        sql::ResultSet* res = bd_.consulta(sql);
        if (res->next()) {
            ret = res->getInt("idGrup");
        }
    }
    catch (sql::SQLException& e) {
        cerr << "SQL Error: " << e.what() << endl;
    }
    return ret;
}

std::string Dades::get_NomGrupMusical(const int id_grup_musical) {
    string ret;
    try {
        string sql = "SELECT nomGrup FROM GrupsMusicals WHERE idGrup = " + to_string(id_grup_musical);
        sql::ResultSet* res = bd_.consulta(sql);
        if (res->next()) {
            ret = res->getString("nomGrup");
        }
    }
    catch (sql::SQLException& e) {
        cerr << "SQL Error: " << e.what() << endl;
    }
    return ret;
}

std::string Dades::get_NomGenere(const int id_genere) {
    string ret;
    try {
        string sql = "SELECT nomGenere FROM Generes WHERE idGenere = " + to_string(id_genere);
        sql::ResultSet* res = bd_.consulta(sql);
        if (res->next()) {
            ret = res->getString("nomGenere");
        }
    }
    catch (sql::SQLException& e) {
        cerr << "SQL Error: " << e.what() << endl;
    }
    return ret;
}

int Dades::get_IDDataSala(const int idSala, const std::string& dia, const std::string& hora_inici, const std::string& hora_fi) {
    int ret = -1;
    try {
        string sql = "SELECT idDataSala FROM DataSala WHERE idSala = " + to_string(idSala)
                     + " AND dia = '" + dia + "' AND hora_inici = '" + hora_inici + "' AND hora_fi = '" + hora_fi + "'";
        sql::ResultSet* res = bd_.consulta(sql);
        if (res->next()) {
            ret = res->getInt("idDataSala");
        }
    }
    catch (sql::SQLException& e) {
        cerr << "SQL Error: " << e.what() << endl;
    }
    return ret;
}

bool Dades::estaSalaDisponible(const int idSala, const int idDataSala) {
    bool ret = false;
    try {
        string sql = "SELECT idEstatSala FROM DisponibilitatSales WHERE idSala = " + to_string(idSala)
            + " AND idDataSala = " + to_string(idDataSala);
        sql::ResultSet* res = bd_.consulta(sql);
        if (res->next()) {
            int estat = res->getInt("idEstatSala");
            if (estat == EstatSala::Disponible) {
                cout << "Disponible" << endl;
                ret = true;
            }
            else {
                cout << "No Disponible" << endl;
            }
        }
    }
    catch (sql::SQLException& e) {
        cerr << "SQL Error: " << e.what() << endl;
    }
    return ret;
}


void Dades::do_afegeix_assaig(const int idGrupMusical, const int idSala, const int idDataSala, const double preu_entrada_public, const int entrades_disponibles) {
    try {
        string sql = "INSERT INTO Assajos(idGrup, idSala, idDataSala, entrades_disponibles, preu_entrada_public) VALUES ("
            + to_string(idGrupMusical)
            + "," + to_string(idSala)
            + "," + to_string(idDataSala) 
            + "," + to_string(entrades_disponibles)
            + "," + to_string(preu_entrada_public)
            + ")";
        bd_.executa(sql);
    }
    catch (sql::SQLException& e) {
        cerr << "SQL Error: " << e.what() << endl;
    }
}

void Dades::modifica_disponibilitat(const EstatSala estat, const int idSala, const int idDataSala) {
    try {
        string sql = "UPDATE DisponibilitatSales SET idEstatSala = " + to_string(estat)
            + " WHERE idSala = " + to_string(idSala) + " AND idDataSala = " + to_string(idDataSala);
        bd_.executa(sql);
    }
    catch (sql::SQLException& e) {
        cerr << "SQL Error: " << e.what() << endl;
    }
}

void Dades::set_SaldoUsuari(const int id_usuari, const float saldo) {
    try {
        string sql = "UPDATE Usuaris SET saldo = " + to_string(saldo)
            + " WHERE idUsuari = " + to_string(id_usuari);
        bd_.executa(sql);
    }
    catch (sql::SQLException& e) {
        cerr << "SQL Error: " << e.what() << endl;
    }
}

void Dades::get_assajos(vector<Assaig> &assajos) {
    try {
        string sql = "SELECT * FROM Assajos";
        sql::ResultSet* res = bd_.consulta(sql);
        if (!res) {
            cout << "Error a l'executar query" << endl;
        }
        else {
            while (res->next()) {
                int idGrup = res->getInt("idGrup");
                int idSala = res->getInt("idSala");
                int idDataSala = res->getInt("idDataSala");
                Assaig assaig;
                assaig.preu_entrada_public = res->getDouble("preu_entrada_public");
                omple_dia_i_hores_data_sala(idDataSala, assaig.dia, assaig.hora_inici, assaig.hora_fi);
                assaig.nom_grup_musical = get_NomGrupMusical(idGrup);
                assaig.nom_sala = get_NomSala(idSala);
                assajos.push_back(assaig);
            }
        }
        delete res;
    }
    catch (sql::SQLException& e) {
        cerr << "SQL Error: " << e.what() << endl;
    }
}

void Dades::get_concerts(vector<Concert>& concerts) {
    try {
        bool ret = false;
        string sql = "SELECT * FROM Concerts";
        sql::ResultSet* res = bd_.consulta(sql);
        if (!res) {
            cout << "Error a l'executar query" << endl;
        }
        else {
            while (res->next()) {
                Concert concert;
                int idGrup = res->getInt("idGrup");
                concert.nom_grup_musical = get_NomGrupMusical(idGrup);
                int idSala = res->getInt("idSala");
                concert.nom_sala = get_NomSala(idSala);
                concert.nom_concert = res->getString("nomConcert");
                concert.dia = res->getString("dia");
                concert.hora = res->getString("hora");
                concert.entrades_disponibles = res->getInt("entrades_disponibles");
                concert.preu = res->getDouble("preu");
                int id_genere = res->getInt("idGenere");
                concert.genere = get_NomGenere(id_genere);
                concerts.push_back(concert);
            }
        }
        delete res;
    }
    catch (sql::SQLException& e) {
        cerr << "SQL Error: " << e.what() << endl;
    }
}

void Dades::omple_dia_i_hores_data_sala(const int idDataSala, std::string& dia, std::string& hora_inici, std::string& hora_fi) {
    try {
        string sql = "SELECT dia, hora_inici, hora_fi FROM DataSala WHERE idDataSala = " + to_string(idDataSala);
        sql::ResultSet* res = bd_.consulta(sql);
        if (res->next()) {
            dia = res->getString("dia");
            hora_inici = res->getString("hora_inici");
            hora_fi = res->getString("hora_fi");
        }
    }
    catch (sql::SQLException& e) {
        cerr << "SQL Error: " << e.what() << endl;
    }

}

void Dades::compra_entrades_assaig(const int id_usuari, const Assaig& assaig, int numero_entrades) {
    Id_Entrades_Assaig info = get_ID_i_entrades_Assaig(assaig);
    if (numero_entrades > info.entrades_disponibles) {
        cout << "Error: ho sento no queden suficients entrades" << endl;
    }
    else {
        cout << "Tenim suficients entrades" << endl;
        float saldo = get_SaldoUsuari(id_usuari);
        if (saldo >= numero_entrades * assaig.preu_entrada_public) {
            cout << "Tens suficient saldo" << endl;
            info.entrades_disponibles -= numero_entrades;
            actualitza_entrades_disponibles_assaig(info.idAssaig, info.entrades_disponibles);
            try {
                string values;
                // idEstatEntrada 5 és Comprada
                for (int i = 0; i < numero_entrades; i++) {
                    values += "(" + to_string(id_usuari) + "," + to_string(info.idAssaig) + "," + to_string(assaig.preu_entrada_public) + ",5)";
                    if (i < numero_entrades - 1)
                        values += ",";
                }
                string sql = "INSERT INTO EntradesAssaig (idUsuari, idAssaig, preu, idEstatEntrada) VALUES "
                    + values;
                bd_.executa(sql);

                set_SaldoUsuari(id_usuari, saldo - numero_entrades * assaig.preu_entrada_public);
            }
            catch (sql::SQLException& e) {
                cerr << "SQL Error: " << e.what() << endl;
            }
        }
        else {
            cout << "Error: no tens prou saldo" << endl;
        }
    }
}

void Dades::compra_entrades_concert(const int id_usuari, const Concert& concert, int numero_entrades) {
    if (numero_entrades > concert.entrades_disponibles) {
        cout << "Error: ho sento no queden suficients entrades" << endl;
    }
    else {
        cout << "Tenim suficients entrades" << endl;
        float saldo = get_SaldoUsuari(id_usuari);
        if (saldo >= numero_entrades * concert.preu) {
            cout << "Tens suficient saldo" << endl;
            int idConcert = get_IDConcert(concert);
            int entrades_disponibles = concert.entrades_disponibles - numero_entrades;
            actualitza_entrades_disponibles_concert(idConcert, entrades_disponibles);
            try {
                string values;
                // idEstatEntrada 5 és Comprada
                for (int i = 0; i < numero_entrades; i++) {
                    values += "(" + to_string(id_usuari) + "," + to_string(idConcert) + "," + to_string(concert.preu) + ",5)";
                    if (i < numero_entrades - 1)
                        values += ",";
                }
                string sql = "INSERT INTO EntradesConcert (idUsuari, idConcert, preu, idEstatEntrada) VALUES "
                    + values;
                bd_.executa(sql);
                set_SaldoUsuari(id_usuari, saldo - numero_entrades * concert.preu);
            }
            catch (sql::SQLException& e) {
                cerr << "SQL Error: " << e.what() << endl;
            }
        }
        else {
            cout << "Error: no tens prou saldo" << endl;
        }
    }
}

int Dades::get_IDConcert(const Concert& concert) {
    int ret = -1;
    try {
        int idSala = get_IDSala(concert.nom_sala);
        string sql = "SELECT idConcert FROM Concerts WHERE idSala = " + to_string(idSala)
            + " AND dia = '" + concert.dia + "'"
            + " AND hora = '" + concert.hora + "'";
        sql::ResultSet* res = bd_.consulta(sql);
        if (res->next()) {
            ret = res->getInt("idConcert");
        }
    }
    catch (sql::SQLException& e) {
        cerr << "SQL Error: " << e.what() << endl;
    }
    return ret;
}

Id_Entrades_Assaig Dades::get_ID_i_entrades_Assaig(const Assaig& assaig) {
    Id_Entrades_Assaig ret = { -1,0 };
    try {
        int idGrupMusical = get_IDGrupMusical(assaig.nom_grup_musical);
        if (idGrupMusical != -1) {
            cout << "id de " << assaig.nom_grup_musical << " es " << idGrupMusical << endl;
            int idSala = get_IDSala(assaig.nom_sala);
            if (idSala != -1) {
                cout << "id de " << assaig.nom_sala << " es " << idSala << endl;
                int idDataSala = get_IDDataSala(idSala, assaig.dia, assaig.hora_inici, assaig.hora_fi);
                if (idDataSala != -1) {
                    string sql = "SELECT idAssajos, entrades_disponibles FROM Assajos WHERE idSala = " + to_string(idSala)
                        + " AND idGrup = " + to_string(idGrupMusical) 
                        + " AND idDataSala = " + to_string(idDataSala);
                    sql::ResultSet* res = bd_.consulta(sql);
                    if (res->next()) {
                        ret.idAssaig = res->getInt("idAssajos");
                        ret.entrades_disponibles = res->getInt("entrades_disponibles");
                    }
                }
            }
        }
    }
    catch (sql::SQLException& e) {
        cerr << "SQL Error: " << e.what() << endl;
    }
    return ret;
}


void Dades::actualitza_entrades_disponibles_assaig(const int assaig_id, const int entrades_disponibles) {
    try {
        string sql = "UPDATE Assajos SET entrades_disponibles = " + to_string(entrades_disponibles)
            + " WHERE idAssajos = " + to_string(assaig_id);
        bd_.executa(sql);
    }
    catch (sql::SQLException& e) {
        cerr << "SQL Error: " << e.what() << endl;
    }
}

void Dades::actualitza_entrades_disponibles_concert(const int concert_id, const int entrades_disponibles) {
    try {
        string sql = "UPDATE Concerts SET entrades_disponibles = " + to_string(entrades_disponibles)
            + " WHERE idConcert = " + to_string(concert_id);
        bd_.executa(sql);
    }
    catch (sql::SQLException& e) {
        cerr << "SQL Error: " << e.what() << endl;
    }
}



