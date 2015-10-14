@ECHO OFF
SET BIN_TARGET=%~dp0/../apigen/apigen/apigen.php
php "%BIN_TARGET%" %*
