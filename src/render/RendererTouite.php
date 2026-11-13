<?php

namespace iutnc\touiteur\render;

use iutnc\touiteur\classes\Touite;
use iutnc\touiteur\classes\Utilisateur;

/**
 * Classe RendererTouite qui permet d'afficher un touite sous différente forme
 */
class RendererTouite {
    /**
     * Méthode rendererCourt
     * @param Touite $touite à afficher
     * @param int $id identifiant du touite
     * @return string touite en format court en code html
     */
    public static function rendererCourt(Touite $touite, int $id) : string
    {
        // comme c'est un affiche court, on affiche que les 100 premiers caractères du touite
        $texteCourt = mb_strimwidth($touite->message, 0, 100, "...");

        // Pour la sécurité on passe tout par filter_var
        $date = filter_var($touite->date, FILTER_SANITIZE_SPECIAL_CHARS);
        $texteCourt = filter_var($texteCourt, FILTER_SANITIZE_SPECIAL_CHARS);

        // On construit le html
        $html = "<article>";
        $html .= "<p>" . self::lienAuteur($touite) . "</p>"; // on peut cliquer sur l'auteur pour voir ses touites
        $html .= "<p>$texteCourt</p>";
        $html .= self::liensTags($touite); // on peut cliquer sur un tag pour voir les touites de ce tag
        $html .= "<p>$date</p>";
        $html .= "</article>";
        $html .= "<a href='index.php?action=detail&id={$touite->id}'>Voir tout le touite</a>"; // on prend bien celui du Dispatcher dans l'index

        return $html;
    }


    /**
     * Methode afficherListeTouite
     * @param array $touites la liste des touites
     * @return string la liste des touites sous le format html
     */
    public static function afficherListeTouite(array $touites) : string
    {
        // Pour afficher les touites dans l'ordre chronologique inverse il suffit de parcourir la liste mais à l'envers
        $touitesInverses = array_reverse($touites);

        // Pour la structure html
        $html = "<div>";

        // On vient afficher chaque touite en affichage court
        foreach ($touitesInverses as $id => $touite) {
            $html .= self::rendererCourt($touite, $id);
        }

        $html .= "</div>";
        return $html;
    }


    /**
     * Méthode pour afficher le touite en detail
     * @param Touite $touite récupère le touite pour l'afficher
     * @return string de touite le pseudo, le message et la date en utilisant un filtre sous le format html
     */
    public static function aff_touite_detail(Touite $touite) : string
    {
        //Filtre le pseudo de l'auteur
        $pseudo = filter_var(
            $touite->auteur->pseudo,
            FILTER_SANITIZE_SPECIAL_CHARS
        );
        //Filtre le message du touite
        $message = filter_var(
            $touite->message,
            FILTER_SANITIZE_SPECIAL_CHARS
        );
        // Filtre la date du touite
        $date = filter_var(
            $touite->date,
            FILTER_SANITIZE_SPECIAL_CHARS
        );

        //Affichage du touite
        $html = "<article>";
        $html .= "<h2>" . self::lienAuteur($touite) . "</h2>";
        $html .= "<p>" . $message . "</p>";
        $html .= self::liensTags($touite);
        // L'image du touite (seulement dans l'affichage détaillé, pas dans la liste)
        if ($touite->image !== null) {
            $image = filter_var($touite->image, FILTER_SANITIZE_SPECIAL_CHARS);
            // la description alternatif de l'image (et s'affiche en dessous si il y en a une)
            if ($touite->descriptionImage !== null && $touite->descriptionImage !== '') {
                $description = filter_var($touite->descriptionImage, FILTER_SANITIZE_SPECIAL_CHARS);
                $html .= "<img src='" . $image . "' alt='" . $description . "'>";
                $html .= "<p class='description-image'>" . $description . "</p>";
            } else {
                $html .= "<img src='" . $image . "' alt='Image du touite'>";
            }
        }
        $html .= "<div>" . $date . "</div>";
        // On ajoute la note du touite avec les 2 boutons pour le noter
        $html .= "<div class='evaluation'>";
        $html .= "<form method='POST' action='index.php?action=evaluer-touite'>";
        $html .= "<input type='hidden' name='idTouite' value='" . $touite->id . "'>";
        $html .= "<button type='submit' class='like' name='choix' value='approuve'>Approuvé</button>";
        $html .= "<span class='score'>" . $touite->note . "</span>";
        $html .= "<button type='submit' class='dislike' name='choix' value='rejete'>Rejeté</button>";
        $html .= "</form>";
        $html .= "<form method='POST' action='index.php?action=suivre-utilisateur'>";
        $html .= "<input type='hidden' name='pseudo' value='" . $pseudo . "'>";
        $html .= "<button type='submit' class='suivre'>Suivre " . $pseudo . "</button>";
        $html .= "</form>";
        $html .= "</div>";
        $html .= "</article>";

        return $html;
    }


    /**
     * Méthode pour afficher les touite d'un utilisateur donné
     * @param Utilisateur $utilisateur pour récuperer ses touites
     * @return string le code HTML de tout les utilisateur
     */
    public static function aff_touite_utilisateur(Utilisateur $utilisateur) : string
    {
        // Récupérer uniquement les touites publiés par cet utilisateur
        $touitesutilisateur = $utilisateur->touitesPublies;

        // Afficher les touites
        return self::afficherListeTouite($touitesutilisateur);
    }

    /**
     * Affiche uniquement les touites associés à un tag donné
     * @param array $touites la liste complète des touites
     * @param string $libelleRecherche le nom du tag recherché (avec ou sans #)
     * @return string le code HTML de la liste filtrée des touites
     */
    public static function afficherTouitesParTag(array $touites, string $libelleRecherche) : string
    {
        $listeDesTouitesParTag = [];
        // On retire les espaces et un # si l'utilisateur l'a saisi seul (possible qu'il soit pas pris en compte a se moment la en tag)
        $libelleRecherche = ltrim(trim($libelleRecherche), '#');


        // on parcours tous les touites de la liste
        foreach ($touites as $touite) {
            $contientTag = false; // On remet à false pour chaque nouveau touite

            // On vérifie si le touite contient le tag recherché (en parcourant le touites), avec le principe fait dans la methode 8
            foreach ($touite->tags as $tag) {
                if ($tag->libelleTag === $libelleRecherche) {
                    $contientTag = true;
                }
            }

            // Si on l'a trouvé, on l'ajoute au tableau résultat
            if ($contientTag) {
                $listeDesTouitesParTag[] = $touite;
            }
        }
        // on retourne la liste filtrée affichée sous forme de HTML grâce au renderer
        return self::afficherListeTouite($listeDesTouitesParTag);
    }


    /**
     * Affiche le mur qui sont les touites suivie par l'utilisateur et/ou sont des tags suivis par l'utilisateur (= abonnée au tag)
     * @param Utilisateur $user l'utilisateur dont on veut afficher le mur
     * @param array $tousLesTouites la liste complète des touites
     * @return string le code HTML du mur
     */
    public static function afficherMurUtilisateur(Utilisateur $user, array $tousLesTouites) : string
    {
        $ListeTouitesMur = [];

        foreach ($tousLesTouites as $touite) {

            // par défaut on affiche pas les tout les touites
            $AfficherSurLeMur = false;

            // si le touite fait partie des auteurs suivis par l'utilisateur
            foreach ($user->utilisateursSuivis as $utilisateurSuivi) {
                if ($touite->auteur->pseudo === $utilisateurSuivi->pseudo) {
                    $AfficherSurLeMur = true;
                }
            }

            // on recherche parmis les tag(s) du touite
            foreach ($touite->tags as $tagDuTouite) {

                // pour chaque tag(s) suivis par l'utilisateur
                foreach ($user->tagsSuivis as $tagAbonne) {
                    // si l'utilisateur à un tag qui est Abonne/Suivie par l'utilisateur
                    if ($tagDuTouite->libelleTag === $tagAbonne->libelleTag) {
                        $AfficherSurLeMur = true;
                    }
                }
            }
            // met le touite dans les touite à afficher, si il est suivie par l'utilisateur et/ou dans les tags suivis (= abonnée au tag)
            if ($AfficherSurLeMur) {
                $ListeTouitesMur[] = $touite;
            }
        }
        // on retourne la liste filtrée affichée sous forme de HTML grâce au renderer
        return self::afficherListeTouite($ListeTouitesMur);
    }

    /**
     * Méthode lienAuteur qui crée le lien vers les touites de l'auteur
     * @param Touite $touite dont on veut l'auteur
     * @return string le lien html vers les touites de l'auteur
     */
    public static function lienAuteur(Touite $touite) : string
    {
        // Pour la sécurité on passe le pseudo par filter_var (et urlencode pour qu'il marche dans l'url)
        $pseudo = filter_var($touite->auteur->pseudo, FILTER_SANITIZE_SPECIAL_CHARS);
        $pseudoUrl = urlencode($touite->auteur->pseudo);

        return "<a href='index.php?action=touites-utilisateur&pseudo=" . $pseudoUrl . "'>" . $pseudo . "</a>";
    }


    /**
     * Méthode liensTags qui crée les liens vers les tags du touite (fonctionnalité 5)
     * @param Touite $touite dont on veut les tags
     * @return string un paragraphe avec un lien par tag (ou rien si le touite n'a pas de tag)
     */
    public static function liensTags(Touite $touite) : string
    {
        // si le touite n'a pas de tag on n'affiche rien
        if (count($touite->tags) === 0) {
            return "";
        }

        $html = "<p>";
        // on fait un lien pour chaque tag du touite
        foreach ($touite->tags as $tag) {
            $libelle = filter_var($tag->libelleTag, FILTER_SANITIZE_SPECIAL_CHARS);
            $libelleUrl = urlencode($tag->libelleTag);
            $html .= "<a href='index.php?action=touites-tag&tag=" . $libelleUrl . "'>#" . $libelle . "</a> ";
        }
        $html .= "</p>";

        return $html;
    }

}