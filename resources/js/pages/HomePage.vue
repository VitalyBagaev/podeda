<template>
  <div>
    <section class="bg-light py-5" style="position:relative; overflow:hidden;">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-8">
            <h1 class="display-4 fw-bold mb-3">Сервисы ИТ ОМК для сотрудников</h1>
            <p class="lead text-muted">Управляйте вашими услугами, оставляйте отзывы и получайте поддержку в одном месте.</p>
          </div>
          <div class="col-lg-4 text-end">
            <img src="/images/hero_bg.png" alt="Background" class="img-fluid rounded" />
          </div>
        </div>
      </div>
    </section>

    <div class="container py-5">
      <section id="categories">
        <h2 class="mb-4">Категории услуг</h2>
        <div v-if="categories.length == 0" class="alert alert-info text-center py-4">
          <p>Категории услуг пока не добавлены.</p>
        </div>
        <div v-else>
          <div class="row g-3">
            <div
              v-for="cat in categories"
              :key="cat.id"
              class="col-12 col-md-4"
              :class="{ active: selectedCategory == cat.id }"
              @click="selectCategory(cat)"
            >
              <div class="card text-center border shadow-sm cursor-pointer"
                :class="selectedCategory == cat.id ? 'border-primary border-2 bg-light' : ''">
                <div class="card-body py-3">
                  <h3 class="h5 mb-0">{{ cat.name }}</h3>
                </div>
              </div>
            </div>
          </div>
          <nav v-if="categoryLastPage > 1" class="d-flex justify-content-center mt-4 gap-2">
            <button type="button" class="btn btn-outline-secondary" @click.prevent="prevCategoryPage" :disabled="categoryPage == 1">Назад</button>
            <span class="text-muted">{{ categoryPage }} / {{ categoryLastPage }}</span>
            <button type="button" class="btn btn-outline-secondary" @click.prevent="nextCategoryPage" :disabled="categoryPage == categoryLastPage">Вперёд</button>
          </nav>
          <p class="mt-3" v-if="selectedCategory">
            <a href="#" @click.prevent="clearCategory" class="text-decoration-none">Показать все отзывы</a>
          </p>
        </div>
      </section>

      <section id="comments">
        <div class="card mb-4 shadow-sm">
          <div class="card-body">
            <h2 class="mb-4">Оставить отзыв</h2>
            <div v-if="!userInfo.id" class="alert alert-danger">
              <a href="#" @click.prevent="changePage('LoginPage')" class="text-decoration-none fw-bold">Войдите</a>
              или
              <a href="#" @click.prevent="changePage('RegisterPage')" class="text-decoration-none fw-bold">зарегистрируйтесь</a>.
            </div>
            <form v-else action="?" method="post">
              <div class="mb-3">
                <label class="form-label">Категория</label>
                <div class="form-control-plaintext">{{ selectedCatName || 'Выберите категорию выше' }}</div>
                <div class="alert alert-danger" v-if="errors.category_id">{{ errors.category_id.join('. ') }}</div>
              </div>
              <div class="mb-3">
                <label class="form-label">Текст</label>
                <textarea v-model="text" rows="4" class="form-control"></textarea>
                <div class="alert alert-danger" v-if="errors.text">{{ errors.text.join('. ') }}</div>
              </div>
              <div class="mb-3">
                <label class="form-label">Фото (макс. 3)</label>
                <input type="file" id="photos" multiple accept="image/*" />
              </div>
              <button type="button" class="btn btn-primary" @click="commentadd">Отправить</button>
            </form>
          </div>
        </div>

        <h2 class="mb-4">{{ commentsTitleText }}</h2>
        <div v-if="comments.length == 0" class="alert alert-info text-center py-4">
          <p>Отзывов пока нет.</p>
        </div>

        <div v-for="comment in comments" :key="comment.id" class="card mb-3 shadow-sm">
          <div class="card-body">
            <div class="d-flex align-items-start gap-3 mb-3">
              <img
                :src="comment.user && comment.user.avatar ? PUBLIC + comment.user.avatar : '/images/default_avatar.png'"
                class="rounded-circle"
                style="width:40px;height:40px;"
                alt=""
              />
              <div class="flex-grow-1">
                <div class="d-flex justify-content-between">
                  <div>
                    <a v-if="userInfo.role == 'admin'" href="#" @click.prevent="changePage('ProfilePage', comment.user_id)" class="text-decoration-none fw-bold text-primary">
                      {{ comment.user ? comment.user.full_name : 'Пользователь' }}
                    </a>
                    <span v-else>{{ comment.user ? comment.user.full_name : 'Пользователь' }}</span>
                    <span class="text-muted ms-2">{{ comment.created_at }}</span>
                  </div>
                  <span class="badge bg-secondary">{{ comment.category ? comment.category.name : '' }}</span>
                </div>
              </div>
            </div>
            <p class="mb-3">{{ comment.text }}</p>
            <div v-if="comment.photos" class="d-flex gap-2 mb-3 flex-wrap">
              <img v-for="photo in comment.photos" :key="photo.id" :src="PUBLIC + photo.photo_path" class="rounded" style="width:100px;height:100px;object-fit:cover;" />
            </div>
            <div class="d-flex gap-2 flex-wrap">
              <button type="button" class="btn btn-outline-secondary btn-sm" @click="like(comment.id)">Лайк {{ comment.likes_count || 0 }}</button>
              <template v-if="userInfo.role == 'admin'">
                <button type="button" class="btn btn-outline-secondary btn-sm" @click="showReply(comment)">Ответить</button>
                <a href="#" class="btn btn-outline-secondary btn-sm" @click.prevent="changePage('EditCommentPage', comment.id)">Редактировать</a>
                <button type="button" class="btn btn-outline-secondary btn-sm" @click="commentdel(comment.id)">Удалить</button>
              </template>
              <template v-else-if="userInfo.id == comment.user_id">
                <a href="#" class="btn btn-outline-secondary btn-sm" @click.prevent="changePage('EditCommentPage', comment.id)">Редактировать</a>
                <button type="button" class="btn btn-outline-secondary btn-sm" @click="commentdel(comment.id)">Удалить</button>
              </template>
            </div>

            <div v-if="replyId == comment.id" class="mt-3">
              <textarea v-model="replyText" rows="3" class="form-control mb-2"></textarea>
              <div class="alert alert-danger" v-if="replyErrors.text">{{ replyErrors.text.join('. ') }}</div>
              <button type="button" class="btn btn-primary btn-sm" @click="answer(comment.id)">Сохранить ответ</button>
              <button type="button" class="btn btn-outline-secondary btn-sm" @click="replyId = null">Отмена</button>
            </div>

            <div v-if="comment.replies && comment.replies[0]" class="mt-3 alert alert-light border-start border-primary border-3 p-3">
              <p class="mb-0"><strong>Ответ администратора:</strong> {{ comment.replies[0].text }}</p>
              <div v-if="userInfo.role == 'admin'" class="d-flex gap-2 flex-wrap mt-2">
                <button type="button" class="btn btn-outline-secondary btn-sm" @click="showReply(comment)">Редактировать ответ</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" @click="answerdel(comment.replies[0].id)">Удалить ответ</button>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script>
export default {
  name: 'HomePage',
  props: ['changePage', 'datasend', 'userInfo', 'PUBLIC'],
  data() {
    return {
      categories: [],
      categoryPage: 1,
      categoryLastPage: 1,
      selectedCategory: null,
      selectedCatName: '',
      comments: [],
      category_id: null,
      text: null,
      errors: {},
      replyId: null,
      replyText: null,
      replyEditId: null,
      replyErrors: {},
      commentsTitleText: 'Отзывы',
    };
  },
  mounted() {
    this.loadCategories();
    this.loadComments();
  },
  methods: {
    loadCategories() {
      this.datasend('categories?page=' + this.categoryPage)
        .then((result) => {
          console.log(result);
          this.categories = result.data || [];
          this.categoryLastPage = result.last_page || 1;
        });
    },
    prevCategoryPage() {
      if (this.categoryPage > 1) {
        this.categoryPage--;
        this.loadCategories();
      }
    },
    nextCategoryPage() {
      if (this.categoryPage < this.categoryLastPage) {
        this.categoryPage++;
        this.loadCategories();
      }
    },
    selectCategory(cat) {
      this.selectedCategory = cat.id;
      this.selectedCatName = cat.name;
      this.category_id = cat.id;
      this.loadComments();
    },
    clearCategory() {
      this.selectedCategory = null;
      this.selectedCatName = '';
      this.category_id = null;
      this.loadComments();
    },
    loadComments() {
      let route = 'comments';
      if (this.selectedCategory) {
        route = 'comments/' + this.selectedCategory;
        this.commentsTitleText = 'Отзывы: ' + this.selectedCatName;
      } else {
        this.commentsTitleText = 'Отзывы';
      }
      this.datasend(route)
        .then((result) => {
          console.log(result);
          this.comments = result.data || result;
        });
    },
    commentadd() {
      this.errors = {};
      let formdata = new FormData();
      if (this.text) formdata.append('text', this.text);
      if (this.category_id) formdata.append('category_id', this.category_id);

      let photos = document.querySelector('#photos');
      if (photos && photos.files[0]) formdata.append('photo', photos.files[0]);
      if (photos && photos.files[1]) formdata.append('photo2', photos.files[1]);
      if (photos && photos.files[2]) formdata.append('photo3', photos.files[2]);

      this.datasend('commentadd', 'POST', formdata)
        .then((result) => {
          console.log(result);
          if (result.errors) {
            this.errors = result.errors;
          }
          if (result.message == 'ok') {
            this.text = null;
            if (photos) photos.value = '';
            this.loadComments();
          }
        });
    },
    like(id) {
      this.datasend('like/' + id, 'POST').then(() => this.loadComments());
    },
    commentdel(id) {
      if (confirm('Удалить?')) {
        this.datasend('commentdel/' + id, 'DELETE').then(() => this.loadComments());
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
      this.replyErrors = {};
      let formdata = new FormData();
      if (this.replyText) formdata.append('text', this.replyText);

      let route = 'answer/' + commentId;
      if (this.replyEditId) {
        route = 'answeredit/' + this.replyEditId;
      }

      this.datasend(route, 'POST', formdata)
        .then((result) => {
          console.log(result);
          if (result.errors) {
            this.replyErrors = result.errors;
          }
          if (result.message == 'ok') {
            this.replyId = null;
            this.loadComments();
          }
        });
    },
    answerdel(id) {
      if (confirm('Удалить ответ?')) {
        this.datasend('answerdel/' + id, 'DELETE').then(() => this.loadComments());
      }
    },
  },
};
</script>
