# differentPasswordGenerator
 An open source PHP Random Password Generator. Works as a standalone library.

# Requirements
- PHP 7.4+

# Customization
You can customize the library however you prefer.
- RandomPassword.php is the class that actually generates the random string
- password-generator.php displays a form with some basic settings

You might want to customize the `useDefaultSettings()` function.
To do so, I advise you to actually extend the RandomPassword class and override useDefaultSettings in the new class. You will need to remove the "final" keyword from the original method. "final" has been used only to prevent accidentally overriding methods and force you to double check what you are doing.

Moreover, please note that you can easily change the two constants:
- `const MIN_LENGTH=8;`
- `const MAX_LENGTH=32;`
This allows you to easily determine a length for your passwords.

# Basic usage
- Clone or download the repository (or the latest release).
- Put the files in your local folders or on your server.
- Open password-generator.php in your browser
- Choose the settings in the displayed form
- A random password is already shown, click on "Generate new" to create a new one.
- Click on "Copy" to copy in your clipboard the current generated string.

If you prefer, you can put RandomPassword.php on (example) your vendor folder, and then put password-generator.php on your public_html. Of course you will need to change the require_once to include the library from the new path.

# Usage as JSON
Suppose you want to use this in AJAX or similar ways. For example, you have a registration form where you want to allow the user to generate a random password on the fly.

You can achieve this by passing ?json=1 to password-generator.php. This way, when you call or reach password-generator.php?json=1 you will receive a JSON response with an object `{'password': 'random_string'}`.

You can then retrieve the string on your application as any other JSON object, using Javascript, jQuery or other languages to make an AJAX request in real time.

Example with a JSON response returning a medium-level 32-characters long random password:
- password-generator.php?send=sent&length=32&securitylevel=2&json=1

Note: send=sent is passed to the url to avoid accidentally calling the script by mistake. Be sure to include it in your query string.

# Why use this?
This library is very lightweight and ready to use. You can use it as a normal form or with an easy call to the JSON format.

# Note
In case you require a password which length is greater than the number of characters used to generate the password itself, the returned password will be shorted. This is actually intended to avoid duplicated characters in the random string.
Example: a low level password uses only lowercase letters from A to Z. Therefore, a 32 characters password will not have enough characters to use, so will generate a shorter string.
Additionally, some characters are removed by default (currently o, O, 0, i, I, 1 which appear similar to eachother and can be confusing).
Of course you can just override `useDefaultSettings()`.