var imagesLength = 11;
var urlBase = 'http://360.ingenuitydesign.com/parsed/si_images/';

var fileStrings = [];

function pad(n, width, z) {
  z = z || '0';
  n = n + '';
  return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
}

function doGrab(fileString, cb) {

  var page = require('webpage').create();
  page.viewportSize = { width: 670, height: 460 };

  var urlPath = [fileString, 'publish', 'web', fileString + '.html'];
  var fullUrl = urlBase + urlPath.join('/');

  page.open(fullUrl, function () {
    console.log('Page opened: ' + fullUrl);

    var frame = 1;
    // Add an interval every 25th second
    var interval = setInterval(function() {
      // Render an image with the frame name
      frame++;
      page.render('export/' + fileString + '/image'+(pad(frame, 3))+'.png', { format: "png" });
      // Exit after 50 images
      if(frame > 60 * 4) {
//        page.render('export/' + fileString + '/image'+(pad(1, 3))+'.png', { format: "png" });
        clearInterval(interval);
        return cb(fileString);
      }
    }, 16);

  });

}

var i = 1;
var base = 'Stat_';


var process = require("child_process")
var spawn = process.spawn
var execFile = process.execFile

function grabCallback() {
  i++;

  var newBase = base,
      newFileString;

  if (i < 10) newBase += '0';
  newFileString = newBase + i;


  console.log('Finished #' + i);
  if (i > imagesLength) {
    phantom.exit();
  } else {
    console.log('Loading ' + newFileString);
    doGrab(newFileString, grabCallback);
  }
}

function doIt() {

  var newBase = base;

  if (i < 10) newBase += '0';
  var fileString = newBase + i;

  console.log('Loading ' + fileString);

  doGrab(fileString, grabCallback);

}

doIt();

console.log('Loading');

//ffmpeg -start_number 10 -i export/Stat_01/image%03d.png -c:v libx264 -r 25 -pix_fmt yuv420p export/Stat_01/animation.mp4
