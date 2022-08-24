import { OsmTrackPopups } from './osm-custom.js';

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


// pagination
const paginationNav = document.querySelector('.nav-links');
const itemsForDiv = document.querySelectorAll(
  '.nav-links > .page-numbers:not(.next):not(.prev)'
);

const newDiv = document.createElement('div');
newDiv.classList.add('newdiv');

itemsForDiv.forEach((item) => {
  newDiv.append(item);
});

const prev = document.querySelector('.page-numbers.prev');
const next = document.querySelector('.page-numbers.next');

if (prev && next) {
  paginationNav.insertBefore(newDiv, next);
} else if (prev) {
  paginationNav.append(newDiv);
} else if (next) {
  paginationNav.prepend(newDiv);
}

document.addEventListener('DOMContentLoaded', function () {
  const osm = new OsmTrackPopups();
  osm.init();
});
