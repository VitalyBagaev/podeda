<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-6">
        <div class="card shadow">
          <div class="card-body p-4">
            <div class="text-center mb-4">
              <img src="/images/unique_omk_it_logo.png" alt="ИТ ОМК" class="img-fluid mb-3" style="max-width: 150px;">
              <h1 class="h3 mb-2"><span class="text-primary">ИТ</span> ОМК</h1>
              <p class="text-muted">Регистрация нового пользователя</p>
            </div>
            <form @submit.prevent="register">
              <div class="mb-3">
                <label for="full_name" class="form-label">ФИО</label>
                <input v-model="full_name" type="text" class="form-control" id="full_name" placeholder="Иванов Иван Иванович">
                <div class="invalid-feedback d-block" v-if="errors.full_name">
                  {{ errors.full_name.join('. ') }}
                </div>
              </div>
              <div class="mb-3">
                <label for="login" class="form-label">Логин</label>
                <input v-model="login" type="text" class="form-control" id="login" placeholder="Придумайте логин">
                <div class="invalid-feedback d-block" v-if="errors.login">
                  {{ errors.login.join('. ') }}
                </div>
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input v-model="email" type="email" class="form-control" id="email" placeholder="example@omk-it.ru">
                <div class="invalid-feedback d-block" v-if="errors.email">
                  {{ errors.email.join('. ') }}
                </div>
              </div>
              <div class="mb-3">
                <label for="phone" class="form-label">Телефон</label>
                <input v-model="phone" type="tel" class="form-control" id="phone" placeholder="+7(999)-999-99-99">
                <div class="invalid-feedback d-block" v-if="errors.phone">
                  {{ errors.phone.join('. ') }}
                </div>
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Пароль</label>
                <input v-model="password" type="password" class="form-control" id="password" placeholder="Минимум 6 символов">
                <div class="invalid-feedback d-block" v-if="errors.password">
                  {{ errors.password.join('. ') }}
                </div>
              </div>
              <button type="submit" class="btn btn-primary w-100">
                Зарегистрироваться
              </button>
            </form>
            <div class="mt-4 text-center">
              <p class="mb-0">Уже есть аккаунт? <a href="#" @click.prevent="changePage('LoginPage')" class="text-decoration-none">Войти</a></p>
            </div>
          </div>
        </div>
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
      avatar: null,
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
            localStorage.setItem('token', result.token);
            this.changePage('HomePage');
          }
        })
        .catch((error) => console.error(error));
    },
  }
}
</script>
