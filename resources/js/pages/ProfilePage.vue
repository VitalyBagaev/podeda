<template>
  <div class="container py-4">
    <div class="row">
      <div class="col">
        <h1 class="mb-4">{{ isAdminView ? 'Профиль пользователя' : 'Личный кабинет' }}</h1>

        <div class="card mb-4 shadow">
          <div class="card-body">
            <div class="row">
              <div class="col-md-4 text-center">
                <img
                  :src="displayUser.avatar ? PUBLIC + displayUser.avatar : '/images/default_avatar.png'"
                  alt="Аватар"
                  class="img-fluid rounded mb-3"
                  style="max-width: 150px; height: 150px; object-fit: cover;"
                />
                <input v-if="isOwnProfile" type="file" id="avatar" accept="image/*" class="form-control form-control-sm" />
              </div>
              <div class="col-md-8">
                <form @submit.prevent="useredit">
                  <div class="mb-3">
                    <label for="full_name" class="form-label">ФИО</label>
                    <input v-if="isOwnProfile" v-model="full_name" type="text" class="form-control" id="full_name" />
                    <div v-else class="form-control-plaintext">{{ displayUser.full_name }}</div>
                    <div class="invalid-feedback d-block" v-if="errors.full_name">{{ errors.full_name.join('. ') }}</div>
                  </div>

                  <div class="mb-3">
                    <label for="login" class="form-label">Логин</label>
                    <div class="form-control-plaintext">{{ displayUser.login }}</div>
                  </div>

                  <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input v-if="isOwnProfile" v-model="email" type="email" class="form-control" id="email" />
                    <div v-else class="form-control-plaintext">{{ displayUser.email }}</div>
                    <div class="invalid-feedback d-block" v-if="errors.email">{{ errors.email.join('. ') }}</div>
                  </div>

                  <div class="mb-3">
                    <label for="phone" class="form-label">Телефон</label>
                    <input v-if="isOwnProfile" v-model="phone" type="tel" class="form-control" id="phone" placeholder="+7(999)-999-99-99" />
                    <div v-else class="form-control-plaintext">{{ displayUser.phone }}</div>
                    <div class="invalid-feedback d-block" v-if="errors.phone">{{ errors.phone.join('. ') }}</div>
                  </div>

                  <button v-if="isOwnProfile" type="submit" class="btn btn-primary">Обновить данные</button>
                </form>
              </div>
            </div>
          </div>
        </div>

        <section v-if="isOwnProfile">
          <h2 class="mb-4">История услуг</h2>
          <div class="card mb-4 shadow">
            <div class="card-body">
              <form @submit.prevent="serviceadd">
                <div class="mb-3">
                  <label for="category_id" class="form-label">Категория</label>
                  <select v-model="category_id" class="form-select" id="category_id">
                    <option :value="null">Выберите</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                  </select>
                  <div class="invalid-feedback d-block" v-if="serviceErrors.category_id">{{ serviceErrors.category_id.join('. ') }}</div>
                </div>

                <div class="mb-3">
                  <label for="service_date" class="form-label">Дата</label>
                  <input type="date" v-model="service_date" class="form-control" id="service_date" />
                  <div class="invalid-feedback d-block" v-if="serviceErrors.service_date">{{ serviceErrors.service_date.join('. ') }}</div>
                </div>

                <button type="submit" class="btn btn-primary">Добавить услугу</button>
              </form>
            </div>
          </div>

          <div v-for="service in displayUser.services" :key="service.id" class="card mb-3 shadow">
            <div class="card-body">
              <h4 class="card-title">{{ service.category ? service.category.name : '' }}</h4>
              <p class="card-text">{{ service.service_date }}</p>
              <button type="button" class="btn btn-outline-danger btn-sm" @click="servicedel(service.id)">Удалить</button>
            </div>
          </div>
        </section>

        <section class="mt-4">
          <h2 class="mb-4">{{ isAdminView ? 'Отзывы пользователя' : 'Мои отзывы' }}</h2>

          <div v-if="!displayUser.comments || displayUser.comments.length == 0" class="card text-center py-4 shadow">
            <p class="mb-0">Отзывов нет.</p>
          </div>

          <div v-for="comment in displayUser.comments" :key="comment.id" class="card mb-3 shadow">
            <div class="card-body">
              <p><b>{{ comment.category ? comment.category.name : '' }}</b> — {{ comment.created_at }}</p>
              <p class="card-text">{{ comment.text }}</p>

              <div class="btn-group" role="group">
                <template v-if="isOwnProfile">
                  <button type="button" class="btn btn-outline-primary btn-sm" @click="changePage('EditCommentPage', comment.id)">Редактировать</button>
                  <button type="button" class="btn btn-outline-danger btn-sm" @click="commentdel(comment.id)">Удалить</button>
                </template>

                <template v-if="isAdminView">
                  <button type="button" class="btn btn-outline-primary btn-sm" @click="showReply(comment)">Ответить</button>
                  <button type="button" class="btn btn-outline-primary btn-sm" @click="changePage('EditCommentPage', comment.id)">Редактировать</button>
                  <button type="button" class="btn btn-outline-danger btn-sm" @click="commentdel(comment.id)">Удалить</button>
                </template>
              </div>

              <div v-if="replyId == comment.id" class="card mt-3">
                <div class="card-body">
                  <textarea v-model="replyText" rows="3" class="form-control mb-2"></textarea>
                  <button type="button" class="btn btn-primary btn-sm" @click="answer(comment.id)">Сохранить ответ</button>
                  <button type="button" class="btn btn-outline-secondary btn-sm" @click="replyId = null">Отмена</button>
                </div>
              </div>

              <div v-if="comment.replies && comment.replies[0]" class="card mt-3">
                <div class="card-body">
                  <p class="card-text"><b>Ответ:</b> {{ comment.replies[0].text }}</p>
                  <div v-if="isAdminView" class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary btn-sm" @click="showReply(comment)">Редактировать ответ</button>
                    <button type="button" class="btn btn-outline-danger btn-sm" @click="answerdel(comment.replies[0].id)">Удалить ответ</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ProfilePage',
  props: ['changePage', 'datasend', 'userInfo', 'getUser', 'pageId', 'PUBLIC'],
  data() {
    return {
      categories: [],
      displayUser: {},
      full_name: null,
      email: null,
      phone: null,
      category_id: null,
      service_date: new Date().toISOString().substr(0, 10),
      errors: {},
      serviceErrors: {},
      isOwnProfile: true,
      isAdminView: false,
      replyId: null,
      replyText: null,
      replyEditId: null,
    };
  },
  mounted() {
    this.loadCategories();
    if (this.userInfo.id) {
      this.loadProfile();
    } else {
      this.getUser().then(() => this.loadProfile());
    }
  },
  methods: {
    loadCategories() {
      this.datasend('categories?per_page=100')
        .then((result) => {
          console.log(result);
          this.categories = result.data || result;
        });
    },
    loadProfile() {
      this.isOwnProfile = !this.pageId || this.pageId == this.userInfo.id;
      this.isAdminView = this.userInfo.role == 'admin' && !this.isOwnProfile;

      if (this.isAdminView) {
        this.datasend('user/' + this.pageId)
          .then((result) => {
            console.log(result);
            this.displayUser = result;
          });
      } else {
        this.displayUser = this.userInfo;
        this.full_name = this.userInfo.full_name;
        this.email = this.userInfo.email;
        this.phone = this.userInfo.phone;
      }
    },
    useredit() {
      this.errors = {};
      let formdata = new FormData();
      if (this.full_name) formdata.append('full_name', this.full_name);
      if (this.email) formdata.append('email', this.email);
      if (this.phone) formdata.append('phone', this.phone);

      let avatar = document.querySelector('#avatar');
      if (avatar && avatar.files[0]) {
        formdata.append('avatar', avatar.files[0]);
      }

      this.datasend('useredit/' + this.displayUser.id, 'POST', formdata)
        .then((result) => {
          console.log(result);
          if (result.errors) {
            this.errors = result.errors;
          }
          if (result.id) {
            this.displayUser = result;
            this.full_name = result.full_name;
            this.email = result.email;
            this.phone = result.phone;
            this.getUser();
          }
        });
    },
    serviceadd() {
      this.serviceErrors = {};
      let formdata = new FormData();
      if (this.category_id) formdata.append('category_id', this.category_id);
      if (this.service_date) formdata.append('service_date', this.service_date);

      this.datasend('serviceadd', 'POST', formdata)
        .then((result) => {
          console.log(result);
          if (result.errors) {
            this.serviceErrors = result.errors;
          }
          if (result.message == 'ok') {
            this.getUser().then((u) => {
              if (u && u.id) {
                this.displayUser = u;
              }
            });
            this.category_id = null;
          }
        });
    },
    servicedel(id) {
      this.datasend('servicedel/' + id, 'DELETE')
        .then(() => {
          this.getUser().then((u) => {
            if (u && u.id) {
              this.displayUser = u;
            }
          });
        });
    },
    reloadComments() {
      if (this.isAdminView) {
        this.datasend('user/' + this.pageId)
          .then((result) => {
            this.displayUser = result;
          });
      } else {
        this.getUser().then((u) => {
          if (u && u.id) {
            this.displayUser = u;
          }
        });
      }
    },
    commentdel(id) {
      if (confirm('Удалить?')) {
        this.datasend('commentdel/' + id, 'DELETE').then(() => this.reloadComments());
      }
    },
    showReply(comment) {
      this.replyId = comment.id;
      if (comment.replies && comment.replies[0]) {
        this.replyEditId = comment.replies[0].id;
        this.replyText = comment.replies[0].text;
      } else {
        this.replyEditId = null;
        this.replyText = null;
      }
    },
    answer(commentId) {
      let formdata = new FormData();
      if (this.replyText) formdata.append('text', this.replyText);

      let route = 'answer/' + commentId;
      if (this.replyEditId) {
        route = 'answeredit/' + this.replyEditId;
      }

      this.datasend(route, 'POST', formdata)
        .then((result) => {
          console.log(result);
          if (result.message == 'ok') {
            this.replyId = null;
            this.reloadComments();
          }
        });
    },
    answerdel(id) {
      if (confirm('Удалить ответ?')) {
        this.datasend('answerdel/' + id, 'DELETE').then(() => this.reloadComments());
      }
    },
  },
};
</script>
