<template>
  <div class="auth-page">
    <div class="auth-card card">
      <div class="text-center mb-8">
        <img src="/images/unique_omk_it_logo.png" alt="ИТ ОМК" class="auth-logo mb-4" />
        <h1 class="logo-text mb-2"><span class="logo-it">ИТ</span> ОМК</h1>
        <p class="text-muted">Регистрация нового пользователя</p>
      </div>
      <form action="?" method="post">
        <div class="form-group">
          <label>ФИО</label>
          <input v-model="full_name" type="text" placeholder="Иванов Иван Иванович" />
          <div class="alert alert-danger" v-if="errors.full_name">
            {{ errors.full_name.join('. ') }}
          </div>
        </div>
        <div class="form-group">
          <label>Логин</label>
          <input v-model="login" type="text" placeholder="Придумайте логин" />
          <div class="alert alert-danger" v-if="errors.login">
            {{ errors.login.join('. ') }}
          </div>
        </div>
        <div class="form-group">
          <label>Email</label>
          <input v-model="email" type="email" placeholder="example@omk-it.ru" />
          <div class="alert alert-danger" v-if="errors.email">
            {{ errors.email.join('. ') }}
          </div>
        </div>
        <div class="form-group">
          <label>Телефон</label>
          <input v-model="phone" type="tel" placeholder="+7(999)-999-99-99" />
          <div class="alert alert-danger" v-if="errors.phone">
            {{ errors.phone.join('. ') }}
          </div>
        </div>
        <div class="form-group">
          <label>Пароль</label>
          <input v-model="password" type="password" placeholder="••••••••" />
          <div class="alert alert-danger" v-if="errors.password">
            {{ errors.password.join('. ') }}
          </div>
        </div>
        <button type="button" class="btn btn-primary w-full mt-4" @click="register">
          Зарегистрироваться
        </button>
      </form>
      <div class="mt-8 text-center">
        <p>Уже есть аккаунт? <a href="#" @click.prevent="changePage('LoginPage')" class="link-red">Войти</a></p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'RegisterPage',
  props: ['changePage', 'datasend', 'changeToken'],
  data() {
    return {
      full_name: null,
      login: null,
      email: null,
      phone: null,
      password: null,
      errors: {},
    }
  },
  methods: {
    register() {
      this.errors = {};
      let formdata = new FormData();
      if (this.full_name) formdata.append('full_name', this.full_name);
      if (this.login) formdata.append('login', this.login);
      if (this.email) formdata.append('email', this.email);
      if (this.phone) formdata.append('phone', this.phone);
      if (this.password) formdata.append('password', this.password);
      
      this.datasend('register', 'POST', formdata)
        .then((result) => {
          console.log(result);
          if (result.errors) {
            this.errors = result.errors;
          }
          if (result.token) {
            this.changeToken(result.token);
            this.changePage('HomePage');
          }
        })
        .catch((error) => console.error(error));
    },
  }
}
</script>
