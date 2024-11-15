# itilucarelli biblio

Un'applicazione web per la gestione di una biblioteca, che consente di cercare e visualizzare libri, oltre a gestire le informazioni relative alle copie, autori, editori, e molto altro.

## Funzionalità

- **Ricerca di libri**: Supporta sia la ricerca di base che quella avanzata per trovare libri tramite titolo, autore, ISBN, genere, editore, ecc.
- **Filtri avanzati**: Permette di applicare filtri personalizzati come la condizione del libro, la disponibilità, il luogo, e altro.
- **Visualizzazione dettagliata**: Ogni libro viene visualizzato con informazioni come autore, editore, scaffale, stato del libro, e numero di copie disponibili.
- **Paginazione**: I risultati della ricerca sono paginati per migliorare l'esperienza utente.
- **Supporto per modalità scura**: L'app supporta la modalità scura tramite un interruttore.

## Requisiti

- **PHP** >= 7.4
- **MySQL** o **MariaDB** per la gestione del database
- **Bootstrap 5** per lo stile del frontend
- **jQuery** per la gestione delle interazioni dinamiche

## Installazione

1. **Clona il repository**:
   ```bash
   git clone https://github.com/masonedotcloud/itilucarelli-biblio.git
   ```

2. **Imposta le credenziali per l'accesso al database**:
   Assicurati di avere un file `db.php` che contenga le credenziali per connettersi al database MySQL.
   ```

3. **Crea il database**:
   Crea un database MySQL utilizzando il file `itilucarelli-biblio.sql` 


3. **Modalità di ricerca**:
   - **Ricerca di base**: Inserisci parole chiave nel campo di ricerca e premi il pulsante di ricerca.
   - **Ricerca avanzata**: Clicca sull'icona del filtro per visualizzare il modulo di ricerca avanzata e applicare filtri personalizzati.

4. **Visualizzazione dei risultati**: I risultati della ricerca vengono visualizzati in una tabella, con informazioni su ogni libro e la possibilità di navigare tra le pagine di risultati.

## Funzionalità Aggiuntive

- **Modalità scura**: Puoi attivare la modalità scura tramite l'interruttore presente nell'interfaccia utente. La modalità verrà applicata su tutte le pagine.

## Tecnologie

- **Backend**: PHP 7.4+
- **Frontend**: HTML, CSS, JavaScript (jQuery), Bootstrap 5
- **Database**: MySQL

## Licenza

Questo progetto è distribuito sotto la Licenza MIT - vedi il file [LICENSE](LICENSE) per ulteriori dettagli.


## Autore

Questo progetto è stato creato da [alessandromasone](https://github.com/alessandromasone).