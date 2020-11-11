# Liga
Liga PHP

# How to work with branches
### Create a new branch
To create a new branch from your current branch, run the following command:
`git checkout -B {BRANCH_NAME}`

### Checkout another branch
To checkout another branch, also a remote branch, just run the following command:
`git checkout {BRANCH_NAME}`

### Show existing local branches
To show all local branches, run the following command:
`git branch`
To see remote branches, I recommend using the GitHub-Webpage: https://github.com/7sven7/Liga/branches/all

### Delete a branch
To delete an existing branch, run the following command:
`git branch -D {BRANCH_NAME}`
This only deletes the branch locally. For deleting remote branches, I would recommend removing it in the GitHub-Webpage, or remove it immediatly after merging pull requests.

# Feature Ideas
Admin-Seite
- Benutzer (Freischalten, Zuweisen zu Teams)
- Ligen (CRUD)
- Teams (CRUD)
- Wochen / MAKs (-> Für alle zukünftigen Wochen übernehmen)
- Kategorien (CRUD)
- Datenpflege + Datenupload

Registrierung

Kumulierte Werte und Wochenwerte einsehbar
Durchschnittswert der anderen Teams neben den eigenen Punkten anzeigen

Excel- / CSV-Export
- Kategorien als Reihen
- Teams (max. 10) als Spalten
