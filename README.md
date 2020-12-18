# teamready.net

[![SymfonyInsight](https://insight.symfony.com/projects/3a88de22-6e3a-4cbf-9252-63e19692c8de/small.svg)](https://insight.symfony.com/projects/3a88de22-6e3a-4cbf-9252-63e19692c8de)

Dépôt pour la nouvelle version de teamready.net

- [Etat d'avancement](#etat-davancement)
- [Structure](#structure)
- [Participer](#participer)
- [Tips](#tips)
- [Références](#r%C3%A9f%C3%A9rences)

## Etat d'avancement
L'état d'avancement peut-être suivi sur [ClickUp](https://clickup.com/) ![Clickup](https://clickup.com/landing/favicons/favicon-16x16.png) mais vous devez être assigné au tableau pour y accéder. Faites-en la demande à [Softy#6425](https://discord.com/users/99008620112388096) ou [Aecy#1290](https://discord.com/users/258295794996609024)

## Structure
Le site est développé avec le [framework Symfony](https://symfony.com/) ![Sf](https://symfony.com/favicons/favicon-16x16.png)
et d'ailleurs l'architecture de base de celui-ci n'a pas été suivi.

Dans le dossier **« Domain »** nous avons la _logique spécifique_ à notre application,
nous avons les **entités**, les **repositories**, les **services**, les **datas**,
les **forms**, les **events** ainsi que les **exceptions**.

Ensuite, **« Infrastructure »** est tout ce qui est la logique fonctionnelle de l'application
qui peuvent être détachée du **« Domain »**.

Egalement, **« Http »** est notre point d'encrage qui communique avec le **« Domain »**.

Certain appelle sa **« L'architecture Hexagonale »** mais ce n'est pas vraiment dans ce cas là.
Ici nous somme générique à **teamready**.

## Participer

Vous pouvez recupérer le projet avec ce dépôt et travailler ainsi afin de faire une pull request.
```shell
$ make dev ## Permet de démarrer le serveur de développement.
$ make seed ## Permet de remplir la base de donnée.
```
Si vous voulez lancer les tests pour vérifier que l'application fonctionne.
```shell
$ make test ## Permet de lancer les tests
```

## Tips
```shell
0%          00
5%          0C
10%         19
15%         26
20%         33
25%         3F
30%         4C
35%         59
40%         66
45%         72
50%         7F
55%         8C
60%         99
65%         A5
70%         B2
75%         BF
80%         CC
85%         D8
90%         E5
95%         F2
100%        FF
```

## Références
Pour évaluer l'éfficacité de la nouvelle version :
* **application.js** : ~~2.425ko~~ / 37ko
* **application.css** : ~~233ko~~ / 45ko
* **images**: ~~2.160ko~~ / 0.21ko
