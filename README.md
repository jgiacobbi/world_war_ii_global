## world_war_ii_global

### Setup

Built on a LAMP stack running php 7.4 (mysql not actually needed). In the project directory run:
```
composer install
npm install
npm run build
```

To clean and build in one step use `npm run rebuild`, or `npm run rebuild-full` to also clear the parcel cache

Setup symlinks in your document root as ROOT/api -> PROJECT/api and ROOT/ui to PROJECT/ui

### Todo
- UI
  - Map
    - Territory names
    - Territory IPC counts
    - Unit and facility placement mechanism
    - Territory textures
  - Lobby
    - Assign lobby members to countries
    - Start game
    - Load game
    - Optional chat support
  - Landing screen
    - Login
    - Start Lobby
    - Join Lobby
    - Join game in progress
  - Game
    - Movement
    - Purchase Units dialogue
    - Unit Placement dialogue
    - Combat dialogue
- Backend
  - Login
  - Start/Load game
  - Game persistence
  - Collect Income
  - Purchase Units
  - Unit Placement
  - Movement validation
  - Combat
- Protocol
  - 95% incomplete, completely open for discussion

### Issues
- The project is not portable because it was developed against one server. Unknown number of issues.
- Websocket disconnects are not managed. Not sure how serious. Sockette might be the solution.
- Current services include apache serving static files, slim REST routes, and websocket JSON-RPC with marginal separation of responsibilities.
