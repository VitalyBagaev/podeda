<template>
  <footer class="footer">
    <div class="container">
      <div class="footer-grid">
        <div class="footer-col">
          <h4>О компании</h4>
          <p>ИТ ОМК - инновационная ИТ-компания, аккредитованная Минцифры России.</p>
        </div>
        <div class="footer-col">
          <h4>Контакты</h4>
          <p>Email: info@omk-it.ru</p>
          <p>Телефон: +7 (351) 000-00-00</p>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; 2026 ИТ ОМК. Все права защищены.</p>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  name: 'FooterComponent',
  props: ['changePage'],
};
</script>
