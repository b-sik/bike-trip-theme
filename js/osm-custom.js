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

  postTitleAndLink = (data, type) => {
    const { permalink, fields } = data;

    const title = fields.locations?.single
      ? fields.locations.start
      : fields.locations.start + ' to ' + fields.locations.end;

    switch (type) {
      case 'marker':
        return `
        <small class="card-title text-dark mb-0"><strong>Day ${fields.day_number}</strong></small>
        <a href="${permalink}" class="card-link">${title}</a>`;
      case 'segment':
        return `
          <h5 class="card-title text-dark">Day ${fields.day_number}</h5>
          <a href="${permalink}" class="card-link h6">${title}</a>`;
      default:
        console.log('type error');
        break;
    }
  };

  popupInnerHTML = (gpx, type) => {
    const { permalink, fields, next_page, prev_page } = gpx;

    this.targetNode.parentElement.classList.remove('ol-popup');
    this.targetNode.parentElement.classList.add('card');

    this.targetNode.classList.add(
      'card-body',
      'd-flex',
      'flex-column',
      'align-items-center',
      'justify-content-center'
    );

    const cardText = (text) => {
      return `<small class="text-dark">${text}</small>`;
    };

    switch (type) {
      case 'marker-prev':
        this.targetNode.innerHTML = `
          ${cardText('Continue onto...')}
          ${this.postTitleAndLink({ permalink, fields }, 'marker')}
          <br />
          ${cardText('or travel back to...')}
          ${this.postTitleAndLink(
            { permalink: prev_page['permalink'], fields: prev_page['fields'] },
            'marker'
          )}
        `;
        break;
      case 'marker-next':
        this.targetNode.innerHTML = `
          ${cardText('Continue onto...')}
          ${this.postTitleAndLink(
            { permalink: next_page['permalink'], fields: next_page['fields'] },
            'marker'
          )}
          <br />
          ${cardText('or travel back to...')}
          ${this.postTitleAndLink({ permalink, fields }, 'marker')}
        `;
        break;
      case 'segment':
        this.targetNode.innerHTML = this.postTitleAndLink(
          { permalink, fields },
          'segment'
        );
        break;
      default:
        console.log('type-error');
        break;
    }
  };

  callback = (mutationsList, observer) => {
    for (let mutation of mutationsList) {
      if (mutation.type === 'childList') {
        gpxData.forEach((gpx) => {
          const { names, descs } = gpx;
          let count = 0;

          names.forEach((name) => {
            if (this.targetNode.textContent.includes(name)) {
              count++;

              if (count === 2) {
                this.popupInnerHTML(gpx, 'segment');
                return;
              }
            }
          });

          descs.forEach((desc, i) => {
            if (this.targetNode.textContent.includes(desc)) {
              if (i === 0 && gpx.hasOwnProperty('prev_page')) {
                this.popupInnerHTML(gpx, 'marker-prev');
              }

              if (i === 1 && gpx.hasOwnProperty('next_page')) {
                this.popupInnerHTML(gpx, 'marker-next');
              }
            }
          });
        });
      }
    }
  };
}
