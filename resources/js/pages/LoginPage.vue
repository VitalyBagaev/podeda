<template>
  <div class="auth-page">
    <div class="auth-card card">
      <div class="text-center mb-8">
        <img src="/images/unique_omk_it_logo.png" alt="ИТ ОМК" class="auth-logo mb-4" />
        <h1 class="logo-text mb-2"><span class="logo-it">ИТ</span> ОМК</h1>
        <p class="text-muted">Вход в систему обратной связи</p>
      </div>
      <form action="?" method="post">
        <div class="form-group">
          <label>Логин</label>
          <input v-model="login" type="text" placeholder="Введите ваш логин" />
          <div class="alert alert-danger" v-if="errors.login">
            {{ errors.login.join('. ') }}
          </div>
        </div>
        <div class="form-group">
          <label>Пароль</label>
          <input v-model="password" type="password" placeholder="••••••••" />
          <div class="alert alert-danger" v-if="errors.password">
            {{ errors.password.join('. ') }}
          </div>
        </div>
        <button type="button" @click="auth" class="btn btn-primary w-full mt-4">
          Войти
        </button>
      </form>

      <div class="mt-8 text-center">
        <p>Нет аккаунта? <a href="#" @click.prevent="changePage('RegisterPage')" class="link-red">Зарегистрироваться</a>
        </p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'LoginPage',
  props: ['changePage', 'datasend', 'changeToken'],
  data() {
    return {
      login: null,
      password: null,
      errors: {},
    }
  },
  methods: {
    auth() {
      this.errors = {};
      let formdata = new FormData();
      if (this.login) formdata.append('login', this.login);
      if (this.password) formdata.append('password', this.password);
      
      this.datasend('login', 'POST', formdata)
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
