document.write('<link rel="icon" type="image/x-icon" href="/favicon.ico" />');// Google Analytics — New Money Muse
(function(){
  var s = document.createElement('script');
  s.async = true;
  s.src = 'https://www.googletagmanager.com/gtag/js?id=G-KNK5M4QRXB';
  document.head.appendChild(s);
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  window.gtag = gtag;
  gtag('js', new Date());
  gtag('config', 'G-KNK5M4QRXB');
})();

(function () {
  var p = window.location.pathname;
  var active = function(path) { return p.indexOf(path) !== -1 ? 'active' : ''; };

  var ytIcon  = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>';
  var igIcon  = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/></svg>';
  var pinIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.373 0 0 5.373 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 0 1 .083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.632-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"/></svg>';

  // Social URLs — update these with your real handles
  var ytUrl  = 'https://www.youtube.com/@NewMoneyMuse-li8qh';
  var igUrl  = 'https://www.instagram.com/newmoneymuse';
  var pinUrl = 'https://www.pinterest.com/nuhaberdasher/';

  var html = '<header id="nmm-header">'
    + '<div class="nmm-header-inner">'
      + '<a href="/" class="nmm-logo">'
        + '<div class="nmm-logo-icon">$<\/div>'
        + '<div class="nmm-logo-name">New Money <span>Muse<\/span><\/div>'
      + '<\/a>'
      + '<nav class="nmm-main-nav">'
        + '<a href="/make-money/best-gig-economy-platforms/" class="' + active('/make-money/') + '">Make Money<\/a>'
        + '<a href="/blog/how-to-build-emergency-fund/" class="' + active('/save-money/') + '">Save Money<\/a>'
        + '<a href="/tools/side-hustle-quiz/" class="' + active('/tools/') + '">Tools<\/a>'
        + '<a href="/blog/how-much-does-doordash-pay/" class="' + active('/blog/') + '">Blog<\/a>'
        + '<a href="/about/" class="' + active('/about/') + '">About<\/a>'
      + '<\/nav>'
      + '<div class="nmm-header-actions">'
        + '<div class="nmm-nav-social">'
          + '<a href="' + ytUrl  + '" target="_blank" rel="noopener noreferrer" aria-label="YouTube">'  + ytIcon  + '<\/a>'
          + '<a href="' + igUrl  + '" target="_blank" rel="noopener noreferrer" aria-label="Instagram">' + igIcon  + '<\/a>'
          + '<a href="' + pinUrl + '" target="_blank" rel="noopener noreferrer" aria-label="Pinterest">' + pinIcon + '<\/a>'
        + '<\/div>'
        + '<a href="/dashboard/" class="nmm-dash-btn">My Dashboard<\/a>'
        + '<button class="nmm-hamburger" onclick="nmmToggleNav()" aria-label="Menu">'
          + '<span><\/span><span><\/span><span><\/span>'
        + '<\/button>'
      + '<\/div>'
    + '<\/div>'
    + '<div class="nmm-mobile-nav" id="nmmMobileNav">'
      + '<a href="/make-money/best-gig-economy-platforms/">\uD83D\uDCB0 Gig Platforms<\/a>'
      + '<a href="/make-money/best-side-hustle-apps/">\uD83D\uDFE2 Side Hustle Apps<\/a>'
      + '<a href="/tools/side-hustle-quiz/">\uD83E\uDDEE Side Hustle Quiz<\/a>'
      + '<a href="/tools/debt-payoff-planner/">\uD83D\uDCB3 Debt Payoff Planner<\/a>'
      + '<a href="/tools/budget-calculator/">\uD83D\uDCCA Budget Calculator<\/a>'
      + '<a href="/tools/savings-calculator/">\uD83D\uDCB0 Savings Calculator<\/a>'
      + '<a href="/blog/how-much-does-doordash-pay/">\uD83D\uDCDD DoorDash Pay Guide<\/a>'
      + '<a href="/blog/how-to-make-money-on-fiverr/">\uD83D\uDCDD Fiverr Beginners<\/a>'
      + '<a href="/blog/how-to-build-emergency-fund/">\uD83D\uDCDD Emergency Fund<\/a>'
      + '<a href="/blog/52-week-savings-challenge/">\uD83D\uDCDD 52-Week Challenge<\/a>'
      + '<a href="/blog/gig-worker-taxes/">\uD83D\uDCDD Gig Taxes<\/a>'
      + '<a href="/about/">About<\/a>'
      + '<a href="/contact/">Contact<\/a>'
      + '<div class="nmm-mobile-social">'
        + '<a href="' + ytUrl  + '" target="_blank" rel="noopener noreferrer" aria-label="YouTube">'  + ytIcon  + '<\/a>'
        + '<a href="' + igUrl  + '" target="_blank" rel="noopener noreferrer" aria-label="Instagram">' + igIcon  + '<\/a>'
        + '<a href="' + pinUrl + '" target="_blank" rel="noopener noreferrer" aria-label="Pinterest">' + pinIcon + '<\/a>'
      + '<\/div>'
      + '<a href="/dashboard/" class="nmm-mobile-dash">My Dashboard \u2192<\/a>'
    + '<\/div>'
  + '<\/header>';

  document.write(html);

  window.nmmToggleNav = function () {
    var nav = document.getElementById('nmmMobileNav');
    if (nav) nav.classList.toggle('open');
  };

  document.addEventListener('click', function (e) {
    var nav = document.getElementById('nmmMobileNav');
    var btn = document.querySelector('.nmm-hamburger');
    if (nav && btn && !nav.contains(e.target) && !btn.contains(e.target)) {
      nav.classList.remove('open');
    }
  });

  window.addEventListener('scroll', function () {
    var h = document.getElementById('nmm-header');
    if (h) h.classList.toggle('scrolled', window.scrollY > 20);
  });
})();
