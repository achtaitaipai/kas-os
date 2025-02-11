const languages = {
  en: "English",
  fr: "Français",
};

const defaultLang = "en";

const ui = {
  en: {
    close: "Close",
    minimize: "Minimize",
    fullscreen: "Toggle Fullscreen",
    back: "Back",
    play: "Play",
    pause: "Pause",
    stop: "Stop",
    mute: "Mute",
    unmute: "Unmute",
    previous: "Previous",
    next: "Next",
  },
  fr: {
    close: "Fermer",
    minimize: "Réduire",
    fullscreen: "Activer/désactiver le mode plein écran",
    back: "Retour",
    play: "Lire",
    pause: "Pause",
    stop: "Arrêter",
    mute: "Désactiver le son",
    unmute: "Réactiver le son",
    previous: "Précédent",
    next: "Suivant",
  },
} as const;

type Lang = keyof typeof ui;

//@ts-ignore
let lang: Lang = document.documentElement.lang;
if (lang in ui) lang = lang as keyof typeof ui;
else lang = defaultLang;

export function t(key: keyof (typeof ui)[keyof typeof languages]) {
  return ui[lang][key] || ui[defaultLang][key];
}
