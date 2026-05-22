<template>
  <footer class="bg-dark text-white py-5">
    <div class="container">
      <div class="row">
        <div class="col-md-6 mb-3 mb-md-0">
          <h4>О компании</h4>
          <p>ИТ ОМК - инновационная ИТ-компания, аккредитованная Минцифры России.</p>
        </div>
        <div class="col-md-6 mb-3 mb-md-0">
          <h4>Контакты</h4>
          <p>Email: info@omk-it.ru</p>
          <p>Телефон: +7 (351) 000-00-00</p>
        </div>
      </div>
      <div class="border-top border-white mt-4 pt-3 text-muted">
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
