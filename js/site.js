import { OsmTrackPopups } from './osm-custom.js';
import { BTT_Pagination } from './btt-pagination.js';
import { BTT_Extra_CSS } from './btt-extra-css.js';

document.addEventListener('DOMContentLoaded', function () {
  const osm = new OsmTrackPopups();
  osm.init();

  const pagination = new BTT_Pagination();
  pagination.init();

  const extraCSS = new BTT_Extra_CSS();
  extraCSS.init();
});
