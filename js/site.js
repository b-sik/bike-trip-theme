// should be css
const tables = document.getElementsByTagName('table');
Array.from(tables).forEach((table) => {
  table.classList.add('table', 'border', 'w-75', 'mx-auto');
  const tds = table.getElementsByTagName('td');
  Array.from(tds).forEach((td) => td.classList.add('border'));
});

// should be css
const blockquotes = document.getElementsByTagName('blockquote');
Array.from(blockquotes).forEach((blockquote) => {
  blockquote.classList.add('blockquote', 'text-center');
  blockquote.lastChild.classList.add('blockquote-footer');
});

jQuery(function ($) {
  // jQuery here
});

// Select the node that will be observed for mutations
var targetNode = document.getElementById('map_ol3js_1_popup-content');

// Options for the observer (which mutations to observe)
var config = { childList: true };

// Callback function to execute when mutations are observed
var callback = function (mutationsList, observer) {
  for (var mutation of mutationsList) {
    if (mutation.type === 'childList') {
      if (typeof gpxData === 'undefined') {
        return;
      }

      gpxData.forEach((gpx) => {
        let count = 0;
        gpx.names.forEach((name) => {
          if (targetNode.textContent.includes(name)) {
            count++;

            if (count === 2) {
              const { permalink, fields } = gpx;
              // should be css
              targetNode.parentElement.style.color = '#fff';
              targetNode.parentElement.style.backgroundColor = '#000';

              targetNode.innerHTML = `
                    <div class="container" style="font-size:0.7rem;">
                        <p>Day ${fields.day_number}</p>
                        <a href="${permalink}">${
                fields.single
                  ? fields.locations.start
                  : fields.locations.start + ' to ' + fields.locations.end
              }</a>     
                    </div>
                `;

              return;
            }
          }
        });
      });
    }
  }
};

// Create an observer instance linked to the callback function
var observer = new MutationObserver(callback);

// Start observing the target node for configured mutations
observer.observe(targetNode, config);