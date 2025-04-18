// Monster_test_bd.cpp : This file contains the 'main' function. Program execution begins and ends there.
//
#include "ConnexioBD.h"
#include <iostream>
#include "Dades.h"
#include <windows.h>
//#include <limits>

using namespace std;

Dades dades;

void disponibilitat_sala() {
    cout << "Afegim disponibilitat" << endl;
    cout << "Entra nom ciutat: ";
    string ciutat;
    getline(cin, ciutat);
    vector<Sala> sales;
    if (dades.get_sales(ciutat, sales)) {
        for (int i = 0; i < sales.size(); ++i) {
            cout << "Sala " << i + 1 << endl;
            cout << sales[i].nom << " " << sales[i].ciutat << " " << sales[i].capacitat << endl;
        }
        cout << "Entra sala: ";
        string sala;
        getline(cin, sala);
        DisponibilitatSala dispo;
        dispo.nom_sala = sala;

        cout << "Entra el dia: " << endl;
        getline(cin, dispo.dia);
        cout << "Entra hora_inici: " << endl;
        getline(cin, dispo.hora_inici);
        cout << "Entra hora_fi: " << endl;
        getline(cin, dispo.hora_fi);

        //dispo.dia = "2025-05-15";
        //dispo.hora_inici = "16:00";
        //dispo.hora_fi = "21:00";
        dades.afegeix_disponibilitat_sala(dispo);
    }
    else {
        cout << "No s'han trobat sales a aquesta ciutat" << endl;
    }
    cout << endl;
}

void reservar_sala() {
    cout << "Fem una reserva" << endl;
    cout << "Entra nom ciutat: ";
    string ciutat;
    getline(cin, ciutat);
    vector<Sala> sales;
    if (dades.get_sales(ciutat, sales)) {
        for (int i = 0; i < sales.size(); ++i) {
            cout << "Sala " << i + 1 << endl;
            cout << sales[i].nom << " " << sales[i].ciutat << " " << sales[i].capacitat << endl;
        }
            
        cout << "Entra el nom del grup musical: " << endl;
        Assaig assaig;
        getline(cin, assaig.nom_grup_musical);
        cout << "Entra el nom de la sala: " << endl;
        getline(cin, assaig.nom_sala);
        cout << "Entra el dia: " << endl;
        getline(cin, assaig.dia);
        cout << "Entra hora_inici: " << endl;
        getline(cin, assaig.hora_inici);
        cout << "Entra hora_fi: " << endl;
        getline(cin, assaig.hora_fi);
        cout << "Entra preu entrada public: " << endl;
        cin >> assaig.preu_entrada_public;
        cin.ignore(1000, '\n');
        dades.afegeix_assaig(assaig);
    }
    else {
        cout << "No s'han trobat sales a aquesta ciutat" << endl;
    }
    cout << endl;
}

void comprar_entrada_assaig() {
    vector<Assaig> assajos;
    dades.get_assajos(assajos);
    if (assajos.size() == 0) {
        cout << "Ho sento no hi ha assajos per comprar entrades" << endl;
    }
    else {
        cout << "Quin assaig vols?" << endl;
        for (int i = 0; i < assajos.size(); i++) {
            // TODO: falta dia i hora assaig
            cout << to_string(i + 1) << " grup: " << assajos[i].nom_grup_musical
                 << " sala: " << assajos[i].nom_sala << endl;
        }
        int assaig_escollit;
        cin >> assaig_escollit;
        cout << "Quantes entrades?";
        int entrades;
        cin >> entrades;
        cin.ignore(1000, '\n');
        dades.compra_entrades_assaig(assajos[assaig_escollit - 1], entrades);

    }
}

void show_menu(const vector<string>& opcions) {
    for (int i = 0; i < opcions.size(); i++) {
        cout << i + 1 << ". " << opcions[i] << endl;
    }
    cout << "Entra opció: " << endl;
}

int main()
{
    SetConsoleOutputCP(CP_UTF8);
    bool sortir = false;
    while (not sortir) {
        cout << "*********************" << endl;
        cout << "    Menú Principal   " << endl;
        cout << "*********************" << endl;

        vector<string> opcions = { "Afegir disponibilitat sala", "Reservar sala", 
            "Comprar entrada assaig", "Sortir" };
        show_menu(opcions);
        string opcio_menu;
        getline(cin, opcio_menu);
        if (opcio_menu == "1") {
            cout << "busquem sala" << endl;
            disponibilitat_sala();
        }
        else if (opcio_menu == "2") {
            cout << "reservem sala" << endl;
            reservar_sala();
        }
        else if (opcio_menu == "3") {
            cout << "comprar entrada assaig" << endl;
            comprar_entrada_assaig();
        }
        else {
            cout << "sortim" << endl;
            sortir = true;
        }
    }

}

