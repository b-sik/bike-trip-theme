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
    const newDiv = this.newDiv();
    const navStyles = ['w-100', 'd-flex', 'flex-column', 'flex-md-row', 'position-relative', 'align-items-center'];

    if (this.prev && this.next) {
      navStyles.push('justify-content-between');
      this.nav.insertBefore(newDiv, this.next);
      this.nav.classList.add(...navStyles);
    } else if (this.prev) {
      this.nav.append(newDiv);
      this.nav.classList.add(...navStyles);
    } else if (this.next) {
      navStyles.push('justify-content-end');
      this.nav.prepend(newDiv);
      this.nav.classList.add(...navStyles);

    }
  };

  newDiv = () => {
    const newDiv = document.createElement('div');
    newDiv.classList.add('pagination-numbers');
    newDiv.classList.add('position-absolute');
    newDiv.style.left = "50%";
    newDiv.style.transform = 'translateX(-50%)';

    this.numbers.forEach((num) => {
      newDiv.append(num);
    });

    return newDiv;
  };
}
