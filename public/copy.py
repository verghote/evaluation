import shutil
from pathlib import Path

from colorama import Fore, Style, init


init(autoreset=True)


# ================= AFFICHAGE =====================

def error(msg):
    print(Fore.RED + "✖ " + msg)


def success(msg):
    print(Fore.GREEN + "✔ " + msg)


def warning(msg):
    print(Fore.YELLOW + "⚠ " + msg)


def info(msg):
    print(Fore.CYAN + "➜ " + msg)


def pause(msg="\nAppuyez sur une touche pour quitter..."):
    input(Fore.YELLOW + msg)


# ================= CONFIGURATION =====================

# Répertoire contenant ce script Python
REPERTOIRE_SCRIPT = Path(__file__).resolve().parent

# Répertoire source
SOURCE = REPERTOIRE_SCRIPT / "evaluation"

# Répertoire destination
DESTINATION = Path("J:/VirtualHostSlam/evaluation")


# ================= INSTALLATION =====================

def installer_evaluation():

    print()
    info("Installation du répertoire evaluation...")
    print()

    # --------------------------------------------------
    # Vérification du répertoire source
    # --------------------------------------------------

    if not SOURCE.exists():

        error(
            f"Le répertoire source n'existe pas : {SOURCE}"
        )

        pause()
        return False

    if not SOURCE.is_dir():

        error(
            f"La source n'est pas un répertoire : {SOURCE}"
        )

        pause()
        return False

    info(f"Source : {SOURCE}")
    info(f"Destination : {DESTINATION}")

    print()

    # --------------------------------------------------
    # Vérification de la destination
    # --------------------------------------------------

    nouveau_dossier = not DESTINATION.exists()

    try:

        # --------------------------------------------------
        # Création du nouveau répertoire
        # --------------------------------------------------

        if nouveau_dossier:

            DESTINATION.mkdir(
                parents=True,
                exist_ok=True
            )

            info("Création du répertoire evaluation...")

        # --------------------------------------------------
        # Suppression de l'ancien contenu
        # --------------------------------------------------

        else:

            warning(
                "Suppression de l'ancien contenu..."
            )

            for element in DESTINATION.iterdir():

                if element.is_dir():

                    shutil.rmtree(element)

                else:

                    element.unlink()

        # --------------------------------------------------
        # Copie du contenu
        # --------------------------------------------------

        info("Copie des fichiers et répertoires...")

        for element in SOURCE.iterdir():

            destination_element = DESTINATION / element.name

            if element.is_dir():

                shutil.copytree(
                    element,
                    destination_element
                )

            else:

                shutil.copy2(
                    element,
                    destination_element
                )

        print()

        # --------------------------------------------------
        # Message selon le type d'installation
        # --------------------------------------------------

        if nouveau_dossier:

            success(
                "Nouveau dossier evaluation créé."
            )

            warning(
                "Ce répertoire nécessite la mise en place "
                "d'un virtual host pour l'utiliser."
            )

        else:

            success(
                "Le répertoire evaluation a été mis à jour."
            )

        print()

        return True

    except PermissionError as e:

        error(
            f"Accès refusé : {e}"
        )

        pause()
        return False

    except OSError as e:

        error(
            f"Erreur lors de l'installation : {e}"
        )

        pause()
        return False

    except Exception as e:

        error(
            f"Erreur inattendue : {e}"
        )

        pause()
        return False


# ================= PROGRAMME PRINCIPAL =====================

def main():

    try:
        installer_evaluation()

    finally:
        print()
        pause()



if __name__ == "__main__":
    main()
