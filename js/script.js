$('a').on('click', function(e){ //Click do tag/elemento  a

    e.preventDefault(); //Não recarrega a página / form

    let vl = $('#valor').val(); //Resgata o valor do input co m o referido ID
    // vlFormat = Math.floor(vl * 100) / 100; // Trunca para 2 casas decimais
    let link = $(this).attr('href'); //Resgata o atributo href da tag a (onde é feito o click)
    location.href = link + '?vl=' + vl; //Faz o redirecionamento com o parâmetro para o pix.php

// console.log(vl);

});
