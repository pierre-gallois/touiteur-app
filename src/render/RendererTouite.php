<?php

namespace iutnc\touiteur\render;

use iutnc\touiteur\classes\Touite;
use iutnc\touiteur\classes\Utilisateur;

/**
 * Classe RendererTouite qui permet d'afficher un touite sous différente forme
 */
class RendererTouite
{
    /**
     * Méthode rendererCourt
     * @param touite à afficher
     * @return touite en format court en code html
     */
    public static function rendererCourt(Touite $touite, int $id): string
    {
        // comme c'est un affiche court, on affiche que les 100 premiers caractères du touite
        $texteCourt = mb_strimwidth($touite->message, 0, 100, "...");

        // Pour la sécurité on passe tout par filter_var
        $pseudo = filter_var($touite->auteur->pseudo, FILTER_SANITIZE_SPECIAL_CHARS);
        $date = filter_var($touite->date, FILTER_SANITIZE_SPECIAL_CHARS);
        $texteCourt = filter_var($texteCourt, FILTER_SANITIZE_SPECIAL_CHARS);

        // On construit le html
        $html = "<a href='index.php?action=detail&id=$id'>";
        $html .= "<article>";
        $html .= "<p>$pseudo</p>";
        $html .= "<p>$texteCourt</p>";
        $html .= "<p>$date</p>";
        $html .= "</article>";
        $html .= "</a>";

        return $html;
    }


    /**
     * Methode afficherListeTouite
     * @param touites la liste des touites
     * @return la liste des touites sous le format html
     */
    public static function afficherListeTouite(array $touites): string
    {
        // Pour afficher les touites dans l'ordre chronologique inverse il suffit de parcourir la liste mais à l'envers
        $touitesInverses = array_reverse($touites);

        // Pour la structure html
        $html = "<div>";

        // On vient afficher chaque touite en affichage court
        foreach ($touitesInverses as $id=>$touite) {
            $html .= self::rendererCourt($touite,$id);
        }

        $html .= "</div>";
        return $html;
    }


    /**
     *méthode  pour afficher le touite en detail
     *param  touite récupere le touite pour l'afficher
     * récupere de touite le pseudo , le message et la date en utilisant un filtre
     */
    public static function  aff_touite_detail(Touite $touite): string{
        //filtre le pseudo de l'auteur
        $pseudo = filter_var(
            $touite->auteur->pseudo,
            FILTER_SANITIZE_SPECIAL_CHARS
        );
        //filtre le message du touite
        $message = filter_var(
            $touite->message,
            FILTER_SANITIZE_SPECIAL_CHARS
        );
        // filtre la date du touite
        $date = filter_var(
            $touite->date,
            FILTER_SANITIZE_SPECIAL_CHARS
        );

        //affichage du touite
        $html = "<article>";
        $html .= "<h2>" . $pseudo . "</h2>";
        $html .= "<p>" . $message . "</p>";
        $html .= "<div>" . $date . "</div>";
        $html .= "</article>";

        return $html;

    }


    /**
     * méthode  pour afficher les touite d'un utilisateur donné
     * param  utilisateur pour récuperer ses touites
     */
    public  static function aff_touite_utilisateur(Utilisateur $utilisateur): string{
        // Récupérer uniquement les touites publiés par cet utilisateur
        $touitesutilisateur = $utilisateur->touitesPublies;

        // Afficher les touites
        return self::afficherListeTouite($touitesutilisateur);
    }


    /**
     * Affiche uniquement les touites associés à un tag donné
     * @param Touite[] $touites la liste complète des touites
     * @param string $libelleRecherche le nom du tag recherché (avec ou sans #)
     * @return string le code HTML de la liste filtrée des touites
     */
    public static function afficherTouitesParTag(array $touites, string $libelleRecherche): string
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
     * @param Touite[] $touites la liste complète des touites
     * @return string le code HTML du mur
     */
    public function afficherMurUtilisateur(Utilisateur $user, array $tousLesTouites) : String
    {
        $ListeTouitesMur = [];

        foreach ($tousLesTouites as $touite){

            // par défaut on affiche pas les tout les touites
            $AchicherSurLeMur = false;

            // si le touite fait partie des auteurs suivis par l'utilisateur
            if (in_array($touite->auteur, $user->utilisateursSuivis)) {
                $AfficherSurLeMur = true;
            }

            // on recherche parmis les tag(s) du touite
            foreach($touite->tagsSuivis as $tagDuTouite){

                // pour chaque tag(s) suivis par l'utilisateur
                foreach ($user->tagsSuivis as $tagAbonne) {
                    // si l'utilisateur à un tag qui est Abonne/Suivie par l'utilisateur
                    if ($tagDuTouite->libelleTag === $tagAbonne->libelleTag) {
                        $AfficherSurLeMur = true;
                    }
                }
            }
            // met le touite dans les touite à afficher, si il est suivie par l'utilisateur et/ou dans les tags suivis (= abonnée au tag)
            if ($AfficherSurLeMur){
                $ListeTouitesMur[] = $touite;
            }
        }
        // on retourne la liste filtrée affichée sous forme de HTML grâce au renderer
        return self::afficherListeTouite($AfficherSurLeMur);
    }
}