const images = document.querySelectorAll('.carousel-image');
let current = 0;

function showNextImage() {
  images[current].classList.remove('active');
  current = (current + 1) % images.length;
  images[current].classList.add('active');
}

setInterval(showNextImage, 4000); // troca a cada 4 segundos

var copy = document.querySelector(".logos-slide").cloneNode(!0);
document.querySelector("#partners").appendChild(copy);
