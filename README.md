# Kas-Os

Kas-Os is a lightweight CMS that lets you easily create and manage a website through a minimalist interface, inspired by the look and feel of Windows XP.

---

## Installation

1. **Download**
   Download the `kas-os.zip` archive from the [latest release](https://github.com/achtaitaipai/kas-os/releases/latest).
2. **Unzip and Copy**
   - Unzip the `kas-os.zip` archive.
   - Copy **all** files to your server, including hidden files such as `/.htaccess` and `public/.htaccess`.
3. **Initial Setup**
   - In your browser, go to: `http://yoursite/admin`.
   - Create your user account.
   - Start adding and managing your content.

---

## Editing Content

- **Content Structure**
  From the admin interface, you can create folders, text files, and links. The structure shown to end users will match what you configure in the admin panel.
- **File Uploads**
  You can upload images or audio files to include in your pages or make them available for download.
- **Hiding Items**
  Any file or folder whose name begins with an underscore (`_`) is hidden from visitors. This allows you to work on drafts or store resources (images, audio, etc.) without making them publicly visible.

---

## Customizing the Site

On the `settings` page, you can customize:

- The **site title**
- The **wallpaper color**
- The **wallpaper image** and its display mode (stretch, tile, center, etc.)

> **Tip:** You can also replace the default icons by modifying the following files in the `public/icons/` directory:
>
> - `audio.png`
> - `directory.png`
> - `image.png`
> - `link.png`

---

## Controlling the Display Order

By default, Kas-Os sorts folders and files from most recent to oldest based on their last modification date.
To define your own display order, rename your items so that their names begin with a number followed by an underscore (`_`). For example:

- `doc.md`
- `chat.jpeg`

become:

- `1_chat.jpeg`
- `2_doc.md`

You can use either positive or negative numbers. In ascending order, items prefixed with negative numbers (e.g., `-1_`) will appear before those with positive numbers. The numeric prefix (e.g., `1_`, `-1_`, etc.) is hidden from the end user.

---

## Password Management

If you forget your password or wish to reset it, manually delete the `accounts.txt` file on your server. The next time you visit `http://yoursite/admin`, you will be prompted to create a new account.
