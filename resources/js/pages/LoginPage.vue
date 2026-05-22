<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-6">
        <div class="card shadow">
          <div class="card-body p-4">
            <div class="text-center mb-4">
              <img src="/images/unique_omk_it_logo.png" alt="ИТ ОМК" class="img-fluid mb-3" style="max-width: 150px;">
              <h1 class="h3 mb-2"><span class="text-primary">ИТ</span> ОМК</h1>
              <p class="text-muted">Вход в систему обратной связи</p>
            </div>
            <form @submit.prevent="auth">
              <div class="mb-3">
                <label for="login" class="form-label">Логин</label>
                <input v-model="login" type="text" class="form-control" id="login" placeholder="Введите ваш логин">
                <div class="invalid-feedback d-block" v-if="errors.login">
                  {{ errors.login.join('. ') }}
                </div>
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Пароль</label>
                <input v-model="password" type="password" class="form-control" id="password" placeholder="••••••••">
                <div class="invalid-feedback d-block" v-if="errors.password">
                  {{ errors.password.join('. ') }}
                </div>
              </div>
              <div class="alert alert-danger" v-if="message">{{ message }}</div>
              <button type="submit" class="btn btn-primary w-100">
                Войти
              </button>
            </form>
            <div class="mt-4 text-center">
              <p class="mb-0">Нет аккаунта? <a href="#" @click.prevent="changePage('RegisterPage')" class="text-decoration-none">Зарегистрироваться</a></p>
            </div>
          </div>
        </div>
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
      message: null,
    }
  },
  methods: {
    auth() {
      this.errors = {};
      this.message = null;
      let formdata = new FormData();
      if (this.login) formdata.append('login', this.login);
      if (this.password) formdata.append('password', this.password);

      this.datasend('auth', 'POST', formdata)
        .then((result) => {
          console.log(result);
          if (result.errors) {
            this.errors = result.errors;
          }
          if (result.message && !result.token) {
            this.message = result.message;
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
