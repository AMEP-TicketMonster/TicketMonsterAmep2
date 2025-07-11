#pragma once

#include <string>

struct Usuari {
    std::string nom;
    std::string cognom;
    std::string email;
    std::string contrasenya;
    float saldo;

    bool operator==(const Usuari& other) const {
        return nom == other.nom &&
            cognom == other.cognom &&
            contrasenya == other.contrasenya &&
            email == other.email &&
            saldo == other.saldo;
    }

    bool operator!=(const Usuari& other) const {
        return !(*this == other);
    }
};

struct Sala {
    std::string nom;
    std::string ciutat;
    std::string capacitat;

    bool operator==(const Sala& other) const {
        return nom == other.nom &&
            ciutat == other.ciutat &&
            capacitat == other.capacitat;
    }

    bool operator!=(const Sala& other) const {
        return !(*this == other);
    }
};

struct DisponibilitatSala {
    std::string nom_sala;
    std::string dia;
    std::string hora_inici;
    std::string hora_fi;
};

enum EstatSala {
    Disponible = 1,
    Reservada = 2
};

struct Assaig {
    std::string nom_sala;
    std::string nom_grup_musical;
    std::string dia;
    std::string hora_inici;
    std::string hora_fi;
    double preu_entrada_public;
};

struct Id_Entrades_Assaig {
    int idAssaig;
    int entrades_disponibles;
};

struct Concert {
    std::string nom_grup_musical;
    std::string nom_sala;
    std::string nom_concert;
    std::string dia;
    std::string hora;
    int entrades_disponibles;
    double preu;
    std::string genere;
};