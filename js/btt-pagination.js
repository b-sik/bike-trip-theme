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
    this.stylePaginationNav();
    this.addAnchorLink();
  };

  stylePaginationNav = () => {
    const numbersWrapper = this.numbersWrapper();
    const navStyles = ['w-100', 'd-flex', 'flex-column', 'flex-md-row', 'position-relative', 'align-items-center'];

    if (this.prev && this.next) {
      navStyles.push('justify-content-between');
      this.nav.insertBefore(numbersWrapper, this.next);
      this.nav.classList.add(...navStyles);
    } else if (this.prev) {
      this.nav.append(numbersWrapper);
      this.nav.classList.add(...navStyles);
    } else if (this.next) {
      navStyles.push('justify-content-end');
      this.nav.prepend(numbersWrapper);
      this.nav.classList.add(...navStyles);
    }
  }

  numbersWrapper = () => {
    const numbersWrapper = document.createElement('div');
    numbersWrapper.classList.add('pagination-numbers');
    numbersWrapper.classList.add('position-absolute');
    numbersWrapper.style.left = "50%";
    numbersWrapper.style.transform = 'translateX(-50%)';

    this.numbers.forEach((num) => {
      numbersWrapper.append(num);
    });

    return numbersWrapper;
  };

  addAnchorLink = () => {
    if (this.prev) {
      const href = this.prev.attributes.href.value;
      console.log(href);
      this.prev.setAttribute('href', href + '#posts-header');
    }
    if (this.next) {
      const href = this.next.attributes.href.value;
      console.log(href);
      this.next.setAttribute('href', href + '#posts-header');
    }
  }
}
