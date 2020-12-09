import map from './mapDrawing.js';

export default {
    joinGame: async function (gameName) {
        try {
            await wsp.RequestResponse({ method: 'joinGame', payload: { name: gameName } });
            return true;
        } catch (meatErr) {
            return false;
        }
    },

    loadExistingGames: async function () {
        try {
            gameList = await wsp.RequestResponse({ method: 'listGames'});
            if (gameList.length > 0) {
                $("#existingGames").html(gameList.join('<br>'));
            }
            
        } catch(meatErr) {
            $("#existingGames").html("Error loading games");
        }

        $("#existingGames").show();
    },

    loginWithName: async function (playerName) {
        return await this.login({username: playerName});
    },

    loginWithKey: async function (session_key) {
        return await this.login({key: session_key});
    },

    login: async function (payloadObj) {
        loginReport = {
            inGame : false,
            login : true
        }

        try {
            response = await wsp.RequestResponse({ method: 'login', payload: payloadObj});

            expirationDate = new Date(response.expiry * 1000);
            Cookies.set('axis-key', response.key, { expires: expirationDate });
            loginReport.inGame = response.inGame;
        } catch (meatErr) {
            loginReport.login = false;
        }

        return loginReport;
    },

    moveToGame: async function () {
        $('#startDiv').hide();
        map.init();
    },

    assembleStartDiv: function () {
        $('#startDiv').html(this.startDivContents());

        // javascript closures don't capture "this"
        let self = this;

        $('#nameButton').on('click', async function () {
            loginReport = await self.loginWithName($('#name').val());

            if (loginReport.login) {
                self.inGameCheck(loginReport.inGame);
            } else {
                alert("Failed to log in, this shouldn't fail");
            }
        });

        $('#gameButton').on('click', async function () {
            if (await self.joinGame($('#game').val())) {
                self.moveToGame();
            } else {
                alert('Failed to join game');
            }
        });
    },

    start: async function () {
        key = Cookies.get('axis-key');

        if (typeof key !== 'undefined') {
            loginReport = await this.loginWithKey(key);

            if (loginReport.login) {
                this.inGameCheck(loginReport.inGame);
            } else {
                alert("Failed to log in, deleting cookies.");
                Cookies.remove('axis-key');
                $('#nameForm').show();
                this.showStartDiv();
            }
        } else {
            $('#nameForm').show();
            this.showStartDiv();
        }
    },

    inGameCheck: function (inGame) {
        if (inGame) {
            this.moveToGame();
        } else {
            this.loadExistingGames();
            $("#nameForm").hide();
            $("#gameForm").show();
            this.showStartDiv();
        }
    },

    showStartDiv: function() {
        $('#startDiv').show();
        document.getElementById('startDiv').scrollIntoView();
    },

    startDivContents: function () {
        return /*html*/`
            <div id="nameForm" style="display:none">
                <label for="name">Enter Name:</label>
                <input type="text" id="name"><br><br>
                <input type="submit" value="Submit Name" id="nameButton">
            </div>
            <div id="gameForm" style="display:none">
                <label for="game">Enter Game:</label>
                <input type="text" id="game"><br><br>
                <input type="submit" value="Submit Game" id="gameButton">
            </div>
            <div id="existingGames" style="display:none">
                No Games Currently Exist
            </div>
        `
    }
}