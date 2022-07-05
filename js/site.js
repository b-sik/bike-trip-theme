const tables = document.getElementsByTagName('table');
Array.from(tables).forEach(table => {
    table.classList.add('table', 'border', 'w-75', 'mx-auto');
    const tds = table.getElementsByTagName('td');
    Array.from(tds).forEach(td => td.classList.add('border'));
});

const blockquotes = document.getElementsByTagName('blockquote');
Array.from(blockquotes).forEach(blockquote => {
    blockquote.classList.add('blockquote', 'text-center');
    blockquote.lastChild.classList.add('blockquote-footer');
})

jQuery(function($){
    // jQuery here
});