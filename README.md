## world_war_ii_global

### Setup

Built on a LAMP stack running php 7.4 (mysql not actually needed). In the project directory run:
```
composer install
npm install
npm run build

```

To run a dev server:
```
# assuming /var/www/html is your webroot
sudo cp web/* /var/www/html
sudo mkdir /var/log/axis
sudo php src/websocket/socket.php
```

To clean and build in one step use `npm run rebuild`, or `npm run rebuild-full` to also clear the parcel cache

### Todo
- UI
  - Map
    - Territory names
    - Territory IPC counts
    - Unit and facility placement mechanism
    - Territory textures
  - Landing screen
    - Invite other players URL
  - Game
    - Select countries
    - Movement
    - Purchase Units dialogue
    - Unit Placement dialogue
    - Combat dialogue
- Backend
  - Collect Income
  - Purchase Units
  - Unit Placement
  - Movement validation
  - Combat
- Protocol
  - 95% incomplete, completely open for discussion

### Issues
- Websocket disconnects are not managed. Not sure how serious. Sockette might be the solution.
