export class OsmTrackPopups {
  constructor() {
    this.targetNode = document.getElementById('map_ol3js_1_popup-content');
    this.observerConfig = { childList: true };
  }

  init() {
    if (typeof gpxData === 'undefined') {
      return;
    }

    const observer = new MutationObserver(this.callback);
    observer.observe(this.targetNode, this.observerConfig);
  }

  popupInnerHTML = (gpx) => {
    const { permalink, fields } = gpx;

    this.targetNode.parentElement.classList.remove('ol-popup');
    this.targetNode.parentElement.classList.add('card');

    this.targetNode.classList.add(
      'card-body',
      'd-flex',
      'flex-column',
      'align-items-center',
      'justify-content-center'
    );

    this.targetNode.innerHTML = `
    <h5 class="card-title text-dark">Day ${fields.day_number}</h5>
    <a href="${permalink}" class="card-link h6">${
      fields.single
        ? fields.locations.start
        : fields.locations.start + ' to ' + fields.locations.end
    }</a>
`;
  };

  callback = (mutationsList, observer) => {
    for (let mutation of mutationsList) {
      if (mutation.type === 'childList') {
        gpxData.forEach((gpx) => {
          let count = 0;
          gpx.names.forEach((name) => {
            if (this.targetNode.textContent.includes(name)) {
              count++;

              if (count === 2) {
                this.popupInnerHTML(gpx);
                return;
              }
            }
          });
        });
      }
    }
  };
}
