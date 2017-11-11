const app = require('./app'),
    commandLineArgs = require('command-line-args'),
    options = commandLineArgs([
      {name: 'port', defaultValue: 3000, type: Number }
    ]);

console.log("Server running at http://127.0.0.1:" + options.port);

app.listen(options.port);
