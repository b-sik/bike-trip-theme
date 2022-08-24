export class BTT_Pagination {
  constructor() {
    this.nav = document.querySelector('.nav-links');
    this.numbers = document.querySelectorAll(
      '.nav-links > .page-numbers:not(.next):not(.prev)'
    );
    this.prev = document.querySelector('.page-numbers.prev');
    this.next = document.querySelector('.page-numbers.next');
  }

  init() {
    if (this.prev && this.next) {
      this.nav.insertBefore(this.newDiv(), this.next);
    } else if (this.prev) {
      this.nav.append(this.newDiv());
    } else if (this.next) {
      this.nav.prepend(this.newDiv());
    }
  };

  newDiv = () => {
    const newDiv = document.createElement('div');
    newDiv.classList.add('pagination-numbers');

    this.numbers.forEach((num) => {
      newDiv.append(num);
    });

    return newDiv;
  };
}
