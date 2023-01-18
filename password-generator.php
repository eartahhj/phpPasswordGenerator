<?php
use PasswordGenerator\RandomPassword;
require_once __DIR__ . '/RandomPassword.php';
?>

<!DOCTYPE html5>
<html>
    <head>
        <title>Password generator</title>
        <meta name="charset" value="utf-8">
    </head>
   
    <body>
        <style type="text/css">
        body { font-size:100%; }
        .info { padding:10px; border-left:2px solid lightblue; background-color:#FCFCFC; margin-top:10px; margin-bottom:10px; }
        .info h2 { font-size:1.2rem; }
        .info ol { list-style-type:none; margin-left:0; padding-left:0; }
        #password { color:#444; font-size:1.5rem; border:1px solid #999; padding:5px; margin-bottom:5px; }
        #password + button { margin-bottom:20px; }
        .field { margin-bottom:10px; }
        form input[type="submit"] { padding:0.25rem; font-size:1.25rem; text-transform:none; border:1px solid #444; border-radius:0.25rem; }
        </style>
        <div id="password-generator">
        <?php
        if ($password->getMessages()) {
            echo $password->renderMessages();
        }
        ?>
        <h1>Random password generator</h1>
        <?php if ($generatedPassword):?>
            <p id="password"><?=$generatedPassword?></p>
        <?php endif?>
        <form action="<?=$_SERVER['PHP_SELF']?>" method="get">
            <div id="buttons" style="margin-bottom:1rem;">
                <button type="submit" name="send" value="sent">Generate new</button>
                <button type="button" name="" onclick="copyPassword('<?=$generatedPassword?>');">Copy</button>
                <span id="copied"></span>
            </div>
            <div class="field field-number">
                <label for="length">Password Length (min: <?=RandomPassword::MIN_LENGTH?> max: <?=RandomPassword::MAX_LENGTH?>)</label>
                <input id="length" name="length" type="number" min="<?=RandomPassword::MIN_LENGTH?>" max="<?=RandomPassword::MAX_LENGTH?>" value="<?=($requestLength?$requestLength:RandomPassword::MIN_LENGTH)?>" required="required" />
            </div>
            <div class="field field-select">
                <label for="securitylevel">Security level</label>
                <select id="securitylevel" name="securitylevel" required="required">
                    <option value=""></option>
                    <?php
                    foreach ($password->getSecurityLevels() as $level=>$name) {
                        echo '<option value="'.$level.'"'.($level==$requestSecurityLevel?' selected="selected"':'').'>'.$name.'</option>';
                    }
                    ?>
                </select>
                <div class="info">
                    <h2>Security levels</h2>
                    <ol>
                        <li>Low: only lowercase letters</li>
                        <li>Medium: lowercase and uppercase letters, numbers</li>
                        <li>High: lowercase and uppercase letters, numbers and some special characters</li>
                    </ol>                
                    
                </div>
            </div>
        </form>
        <script type="text/javascript">
        function copyPassword(password)
        {
            navigator.clipboard.writeText(password);
            document.getElementById('copied').textContent = 'Copied!';
            setTimeout(function() {
                document.getElementById('copied').textContent = '';
            }, 1000);
        }
        </script>
    </body>
</html>