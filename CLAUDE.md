# CLAUDE.md — 4Learning

Contexte projet pour éviter de tout relire à chaque session.

---

## Présentation

Forum Q&R étudiant entre élèves et profs — Université Savoie Mont Blanc, Groupe 11, L1 MISPI.
Les étudiants posent des questions, les élèves de niveau supérieur répondent, les réponses sont notées 1-5 étoiles.

---

## Stack

- **Backend** : PHP 8+ (PDO, sessions, JSON API)
- **BDD** : MySQL, base `db_grp11` (user `grp11`)
- **Frontend** : HTML/CSS vanilla + JS vanilla (pas de framework)
- **Pas de Composer, pas de npm**

---

## Architecture des fichiers

```
index.php                        ← Page d'accueil (hero, stats, modals login/register)
pages/
  questions.php                  ← Liste + filtres + modal nouvelle question + modal détail
  classement.php                 ← Podium top 3 + tableau + badges saison
  profil.php                     ← Profil user connecté (stats, badges, activité)

php/
  db_connect.php                 ← PDO singleton, constantes DB_HOST/NAME/USER/PASS/CHARSET
  template/
    header.php                   ← <head>, <navbar> ; attend $page_title, $active_page, $base_path
    footer.php                   ← </body>, charge $page_script si défini

  mvc_users/
    crud_users.php               ← POST action=register|login|logout
    vue_users.php                ← GET ?user_id=X → stats + badges + questions + réponses du user
    admin_users.php              ← GET → stats globales (nb users/questions/réponses)

  mvc_questions/
    crud_questions.php           ← POST action=create (auth requise, +2 pts)
    vue_questions.php            ← GET ?tag_id=&statut=&search= → liste 50 questions
    admin_questions.php          ← POST action=resolve|close & question_id (auteur seulement)

  mvc_reponses/
    crud_reponses.php            ← POST action=create (+5 pts, vérifie niveau) | rate (1-5 étoiles)
    vue_reponses.php             ← GET ?question_id=X → réponses triées par note_moy DESC
    admin_reponses.php           ← GET → classement saisonnier (top 20 users + badges)

  mvc_tags/
    crud_tags.php                ← GET → liste tous les tags (pas de paramètre)
    vue_tags.php                 ← GET ?tag_id=X → tag + ses questions
    admin_tags.php               ← POST action=create|delete (ADMIN seulement, vérifie role='admin')

  mvc_badges/
    crud_badges.php              ← GET ?user_id=X → attribue automatiquement les badges mérités
    vue_badges.php               ← GET → liste tous les badges
    admin_badges.php             ← POST action=award_top3 & saison_id (auth requise)

assets/
  css/style.css
  js/
    script.js                    ← JS page d'accueil (login/register/stats)
    questions.js                 ← JS page questions
    classement.js                ← JS page classement
    profil.js                    ← JS page profil

sql/4learning.sql                ← Script création BDD complet
```

---

## Schéma BDD (résumé)

| Table | Colonnes clés |
|---|---|
| `users` | id, username, email, password_hash, niveau (1-5), points, fiabilite (0-100), avatar, **role** (user\|admin), created_at |
| `tags` | id, nom, couleur (#hex) |
| `questions` | id, user_id, tag_id, titre, contenu, niveau_requis, statut (ouverte\|resolue\|fermee), created_at |
| `answers` | id, question_id, user_id, contenu, note_moy, nb_votes, created_at |
| `ratings` | id, answer_id, user_id, note (1-5) — UNIQUE (answer_id, user_id) |
| `badges` | id, nom, description, icone, couleur |
| `user_badges` | id, user_id, badge_id, saison_id, obtenu_le |
| `saisons` | id, nom, date_debut, date_fin, active |

---

## Session & Auth

- Session PHP côté serveur (`$_SESSION['user_id']`, `username`, `niveau`)
- Session JS côté client : `localStorage.getItem('4learning_user')` → objet `{id, username, niveau, points, fiabilite}`
- Pas de JWT, pas de cookie personnalisé

---

## Système de points / fiabilité

- Poser une question : **+2 pts**
- Donner une réponse : **+5 pts**
- Fiabilité user = moyenne de `note_moy` de **toutes ses réponses ayant au moins 1 vote** × 20 (0-100)

---

## Bugs corrigés (session 2026-04-14)

### 1. `php/db_connect.php` — constante `DB_CHARSET` manquante
- **Problème** : `DB_CHARSET` utilisé dans le DSN mais jamais défini → erreur PHP fatale
- **Correction** : ajout de `define('DB_CHARSET', 'utf8mb4');`

### 2. `php/mvc_reponses/crud_reponses.php` — fiabilité calculée sur 1 seule réponse
- **Problème** : après un vote, la fiabilité du répondant était mise à jour avec la `note_moy` de la réponse notée seulement
- **Correction** : calcul sur `AVG(note_moy)` de **toutes** les réponses du user ayant `nb_votes > 0`

### 3. `php/mvc_tags/admin_tags.php` — aucun contrôle de rôle admin
- **Problème** : tout user connecté pouvait créer/supprimer des tags
- **Correction** : vérification `role = 'admin'` dans la table `users` + ajout colonne `role ENUM('user','admin') DEFAULT 'user'` dans `sql/4learning.sql`
- **Note** : pour créer un admin, faire manuellement `UPDATE users SET role='admin' WHERE id=X;` en BDD

---

## Design CSS (redesign 2026-04-14)

**Concept : Néobrutalism académique** — remplace l'esthétique SaaS vert générique

Principes :
- Fond **crème** `#FFFBF0` au lieu du blanc
- **Ombres portées solides** `4px 4px 0 #14080E` (pas de blur)
- **Bordures épaisses** `2.5px solid #14080E` partout
- **Navbar verte** `#53dd6c` avec texte noir (inversé vs ancienne navbar sombre)
- **Zéro gradient** — hero avec motif de points CSS (`radial-gradient`)
- **Page-header** avec motif de hachures diagonales CSS
- **Typographie mixte** : `Georgia` (serif) pour titres, sans-serif pour corps, `monospace` pour chiffres/tags/labels techniques
- **Avatars/podium carrés** (border-radius: 3-4px) au lieu de cercles
- Boutons `text-transform: uppercase`, quasi-carrés
- Fiabilité gauge carrée (pas de pilule)
- Badges question : accent bordure gauche épaisse (5px) colorée par matière

Variables CSS clés ajoutées : `--creme`, `--vert-pale`, `--font-serif`, `--font-mono`, `--border`, `--shadow-vert`

---

## Points d'attention futurs

- `admin_badges.php` vérifie `$_SESSION['user_id']` mais pas le rôle admin (cohérence à prévoir)
- `vue_questions.php` : LIMIT 50 en dur, pas de pagination
- Les données de démo JS dans `questions.js` (DEMO_QUESTIONS) peuvent être retirées quand la BDD est opérationnelle
