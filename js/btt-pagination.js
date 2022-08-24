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
    const navStyles = ['w-100', 'd-flex', 'position-relative'];

    if (this.prev && this.next) {
      navStyles.push('justify-content-between');
      this.nav.insertBefore(this.newDiv(), this.next);
      this.nav.classList.add(...navStyles);
    } else if (this.prev) {
      this.nav.append(this.newDiv());
      this.nav.classList.add(...navStyles);
    } else if (this.next) {
      navStyles.push('justify-content-end');
      this.nav.prepend(this.newDiv());
      this.nav.classList.add(...navStyles);
    }
  };

  newDiv = () => {
    const newDiv = document.createElement('div');
    newDiv.classList.add('pagination-numbers');
    newDiv.style.position = 'absolute';
    newDiv.style.left = "50%";
    newDiv.style.transform = 'translateX(-50%)';

    this.numbers.forEach((num) => {
      newDiv.append(num);
    });

    return newDiv;
  };
}
