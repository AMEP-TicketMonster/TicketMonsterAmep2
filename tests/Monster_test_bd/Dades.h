#pragma once
#include "ConnexioBD.h"
#include <string>
#include <vector>
#include "tipus_dades.h"


class Dades
{
public:

    Dades();

    // Crides d'usuari loggejat->gestio_usuaris
    void crea_usuari(const Usuari& usuari);
    bool get_usuari(const std::string& nom, Usuari& usuari);
    void esborra_usuari(const std::string& nom);
    int get_IdUsuari(const std::string& email, const std::string& contrasenya);


    // Assatjos
    bool get_sales(const std::string& ciutat, std::vector<Sala>& sales);
    void afegeix_disponibilitat_sala(const DisponibilitatSala& dispo);

    void afegeix_assaig(const Assaig& assaig);
    void get_assajos(std::vector<Assaig>& assajos);
    void compra_entrades_assaig(const int id_usuari, const Assaig & assaig, int numero_entrades);
    void compra_entrades_concert(const int id_usuari, const Concert& concert, int numero_entrades);

    void get_concerts(std::vector<Concert>& concerts);
private:
    ConnexioBD bd_;

    int afegeix_dataSala(int idData, const std::string& dia, const std::string& hora_inici, const std::string& hora_fi);
    int get_IDSala(const std::string& nom);
    int get_CapacitatSala(const std::string& nom);
    std::string get_NomSala(const int id_sala);
    int get_IDGrupMusical(const std::string& nom_grup);
    std::string get_NomGrupMusical(const int id_grup_musical);
    std::string get_NomGenere(const int id_genere);
    int get_IDDataSala(const int idSala, const std::string& dia, const std::string& hora_inici, const std::string& hora_fi);
    bool estaSalaDisponible(const int idSala, const int idDataSala);
    void do_afegeix_assaig(const int idGrupMusical, const int idSala, const int idDataSala, const double preu_entrada_public, const int entrades_disponibles);
    void modifica_disponibilitat(const EstatSala estat, const int idSala, const int idDataSala);
    Id_Entrades_Assaig get_ID_i_entrades_Assaig(const Assaig& assaig);
    void actualitza_entrades_disponibles_assaig(const int assaig_id, const int entrades_disponibles);
    void actualitza_entrades_disponibles_concert(const int concert_id, const int entrades_disponibles);
    void omple_dia_i_hores_data_sala(const int idDataSala, std::string& dia, std::string& hora_inici, std::string& hora_fi);
    float get_SaldoUsuari(const int id_usuari);
    void set_SaldoUsuari(const int id_usuari, const float saldo);
    int get_IDConcert(const Concert& concert);
};

