const cla = require('command-line-args');

let options = cla([
    { name: 'port', defaultValue: 3000, type: Number}
]);


console.log(options);
