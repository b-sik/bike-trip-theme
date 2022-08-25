export class BTT_Extra_CSS {
  constructor() {
    this.blockquotes = document.getElementsByTagName('blockquote');
    this.tables = document.getElementsByTagName('table');
  }

  init() {
    this.tablesCSS();
    this.blockquotesCSS();
  }

  tablesCSS = () => {
    Array.from(this.tables).forEach((table) => {
      table.classList.add('table', 'border', 'w-75', 'mx-auto');
      const tds = table.getElementsByTagName('td');
      Array.from(tds).forEach((td) => td.classList.add('border'));
    });
  };

  blockquotesCSS = () => {
    Array.from(this.blockquotes).forEach((blockquote) => {
      blockquote.classList.add('blockquote', 'text-center');
      blockquote.lastChild.classList.add('blockquote-footer');
    });
  };
}
