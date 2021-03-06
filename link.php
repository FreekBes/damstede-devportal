<!DOCTYPE html>
<html lang="nl">
<head>
    <title>Log in via Zermelo</title>
    <?PHP include_once("import/headers.html"); ?>
    <script type="module">
        import QrScanner from './import/qr-scanner.min.js';
        QrScanner.WORKER_PATH = 'import/qr-scanner-worker.min.js';
        var usingQr = false;
        var scanner = false;
        QrScanner.hasCamera().then(function(hasCamera) {
            if (hasCamera) {
                scanner = new QrScanner(document.getElementById('qr-preview'), function(result) {
                    console.log(result);
                    try {
                        var zResult = JSON.parse(result);
                        if (Object.keys(zResult).indexOf("code") == -1 || Object.keys(zResult).indexOf("institution") == -1) {
                            alert("Ongeldige Zermelo QR-code");
                        }
                        else {
                            document.getElementById("loading").style.display = "table";
                            document.getElementById("loading").style.display = "none";
                            if (zResult["institution"] === "damstedelyceum") {
                                pauseCameraAccess();
                                console.log("QR-scanner gestopt");
                                document.getElementById('zermelo-code').value = zResult["code"];
                                showAction('zermelolink');
                                submitZermelo();
                            }
                            else {
                                alert("School wordt niet ondersteund. Dit portaal is alleen bedoeld voor het Damstede Lyceum.");
                            }
                        }
                    }
                    catch(e) {
                        console.log(e);
                        alert("Ongeldige Zermelo QR-code");
                    }
                });
                document.getElementById("signin-btn-camera").disabled = false;
                document.getElementById("signin-btn-camera").removeAttribute("title");
            }
            else {
                document.getElementById("signin-btn-camera").disabled = true;
                document.getElementById("signin-btn-camera").setAttribute("title", "Geen camera beschikbaar, of je browser wordt niet ondersteund. Gebruik een toegangscode om in te loggen op dit apparaat.");
            }
        });
        window.requestCameraAccess = function() {
            if (scanner !== false) {
                document.getElementById("loading").style.display = "table";
                scanner.start().then(function() {
                    console.log("QR-scanner gestart");
                    showAction('qrscanner');
                    document.getElementById("loading").style.display = "none";
                    usingQr = true;
                }).catch(function(e) {
                    alert("Geen camera's gevonden om te gebruiken, of je hebt geen toegang tot je camera gegeven.\nGebruik een toegangscode om in te loggen op dit apparaat.");
                    document.getElementById("loading").style.display = "none";
                    document.getElementById("signin-btn-camera").disabled = true;
                    document.getElementById("signin-btn-camera").setAttribute("title", "Geen camera beschikbaar, of je browser wordt niet ondersteund. Gebruik een toegangscode om in te loggen op dit apparaat.");
                });
            }
            else {
                alert("Geen camera's gevonden om te gebruiken, of je hebt geen toegang tot je camera gegeven.\nGebruik een toegangscode om in te loggen op dit apparaat.");
                document.getElementById("loading").style.display = "none";
                document.getElementById("signin-btn-camera").disabled = true;
                document.getElementById("signin-btn-camera").setAttribute("title", "Geen camera beschikbaar, of je browser wordt niet ondersteund. Gebruik een toegangscode om in te loggen op dit apparaat.");
            }
        }
        window.pauseCameraAccess = function() {
            scanner.pause();
        }
    </script>
    <script>
    function getCookie(name) {
		var re = new RegExp(name + "=([^;]+)");
		var value = re.exec(document.cookie);
		return (value != null) ? unescape(value[1]) : null;
    }
    </script>
</head>
<body onload="document.getElementById('loading').style.display = 'none';">
    <header>
        <h1 id="pagetitle">Log in via Zermelo - Damstede Device Portaal<span class="extra-info"> voor Docenten</span></h1>
        <div id="pageoptions">
            <div class="awesome" id="manual" title="Handleiding openen (PDF)" onclick="window.open('HandleidingDevicePortalDamstede.pdf');">&#xf02d;</div>
        </div>
    </header>
    <div id="content" class="extrapadding">
        <p><b>Om gebruik te kunnen maken van het Device Portaal en een iPad-kar of computerlokaal voor je les te reserveren, moet je inloggen via Zermelo:</b></p>
        <ol>
            <li>Open het <a href="https://damstedelyceum.zportal.nl/main/#connectionsModule-connectApp" target="_blank">Zermelo Portal</a> en log in</li>
            <li>Ga naar menu Koppelingen</li>
            <li>Druk op Koppel App. Je ziet nu een pagina met een QR-code en een toegangscode.</li>
            <li>Kies hieronder hoe je wilt inloggen: door de QR-code te scannen, of handmatig de toegangscode in te vullen.</li>
        </ol>
        <div id="signin-chooser">
            <button id="signin-btn-camera" onclick="requestCameraAccess();" disabled title="Geen camera beschikbaar, of je browser wordt niet ondersteund. Gebruik een toegangscode om in te loggen op dit apparaat.">QR-code scannen</button>
            <button id="signin-btn-code" onclick="showAction('zermelolink');">Handmatig inloggen</button>
        </div>

        <script>
        function submitZermelo() {
            console.log("Form submit");
            document.getElementById('loading').style.display = 'table';
            var form = document.getElementById('zermelo-code-form');
            var loginError = document.getElementById("loginerror-msg");
            var data = new FormData(form);
            var xhr = new XMLHttpRequest();
            xhr.open('POST', form.getAttribute('action'), true);
            xhr.onload = function() {
                document.getElementById('loading').style.display = 'none';
                if (xhr.status !== 200) {
                    loginError.innerHTML = "Serverfout "+xhr.statusText+". Probeer het later opnieuw.";
                    showAction('loginerror');
                }
                else {
                    var loginResponse = JSON.parse(xhr.responseText);
                    switch (loginResponse.type) {
                        case "success": {
                            document.getElementById('loading').style.display = 'table';
                            window.location.href = "portal.php";
                            break;
                        }
                        default: {
                            loginError.innerHTML = loginResponse.message;
                            showAction('loginerror');
                            break;
                        }
                    }
                }
            };
            xhr.send(data);
        }
        </script>

        <div class="action" id="loginerror" style="display: none;">
            <div class="inneraction">
                <div class="actioncontent">
                    <div class="actionheader">Er ging iets fout</div>
                    <div class="actionclose" data-action="loginerror" onclick="hideAction(this); if (usingQr) { requestCameraAccess(); }">&#x2716;</div>
                    <p id="loginerror-msg">Een onbekende fout is opgetreden. Probeer het later opnieuw.</p>
                    <div class="actionbuttons">
    <input class="button" type="button" value="Oké" data-action="loginerror" onclick="hideAction(this); if (usingQr) { requestCameraAccess(); }" />
                    </div>
                </div>
            </div>
        </div>

        <div class="action" id="qrscanner" style="display: none;">
            <div class="inneraction">
                <div class="actioncontent">
                    <div class="actionheader">QR-code scannen</div>
                    <div class="actionclose" data-action="qrscanner" onclick="hideAction(this); pauseCameraAccess();">&#x2716;</div>
                    <video id="qr-preview"></video>
                    <div class="actionbuttons">
                        <input class="button extra" type="button" value="Annuleren" data-action="qrscanner" onclick="hideAction(this); pauseCameraAccess();" />
                    </div>
                </div>
            </div>
        </div>

        <div class="action" id="zermelolink" style="display: none;">
            <div class="inneraction">
                <div class="actioncontent">
                    <form id="zermelo-code-form" action="import/link-cb.php" method="post" target="_self" accept-charset="utf-8" autocomplete="off">  
                        <div class="actionheader">Toegangscode invullen</div>
                        <div class="actionclose" data-action="zermelolink" onclick="hideAction(this);">&#x2716;</div>
                        <table class="actiontable">
                            <tr>
                                <th>Schoolnaam</th>
                                <td><input required type="text" readonly id="zermelo-school" name="zermelo-school" autocomplete="off" placeholder=">schoolnaam<.zportal.nl" size="65" value="damstedelyceum" /></td>
                            </tr>
                            <tr>
                                <th>Toegangscode</th>
                                <td><input required type="text" id="zermelo-code" name="zermelo-code" autocomplete="off" placeholder="123 456 789 123" size="65" maxlength="15" value="" onpaste="spaceUpZermeloCode(event);" oninput="spaceUpZermeloCode(event);" onchange="spaceUpZermeloCode(event);" /></td>
                            </tr>
                        </table>
                        <script>
                        function spaceUpZermeloCode(event) {
                            var i = event.target.value;
                            i = i.replace(/[^0-9]/g, "").split(" ").join("").substr(0, 12).replace(/(.{3})/g, '$1 ').trim();
                            event.target.value = i;
                        }
                        </script>
                        <div class="actionbuttons">
                            <input class="button extra" type="button" value="Annuleren" data-action="zermelolink" onclick="hideAction(this);" />
                            <input class="button" type="button" value="Inloggen" name="login" onclick="submitZermelo();"/>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="loading" style="display: table; width: 100%; height: 100%; position: fixed; top: 0px; left: 0px; right: 0px; bottom: 0px; background-color: rgba(0,0,0,0.8);">
		<div style="display: table-cell; vertical-align: middle;">
			<div style="margin-left: auto; margin-right: auto; text-align: center;">
				<div id="spinner"></div>
			</div>
		</div>
    </div>
    
    <script>
    if (getCookie("zat") != null && getCookie("zei") != null && getCookie("zs") != null && getCookie("zat") != undefined && getCookie("zei") != undefined && getCookie("zs") != undefined && getCookie("zat") != "" && getCookie("zei") != "" && getCookie("zs") != "") {
        console.log("Logging in via cookies...");
        submitZermelo();
    }
    </script>
</body>
</html>