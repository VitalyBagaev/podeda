<template>
  <div class="container py-5">
    <h1 class="mb-4">Админ-панель</h1>

    <div class="d-flex gap-2 mb-4">
      <button type="button" class="btn" :class="tab == 'comments' ? 'btn-primary' : 'btn-outline-secondary'" @click="tab = 'comments'">Комментарии</button>
      <button type="button" class="btn" :class="tab == 'users' ? 'btn-primary' : 'btn-outline-secondary'" @click="tab = 'users'">Пользователи</button>
    </div>

    <div v-if="tab == 'comments'" class="mb-4">
      <div v-for="comment in comments" :key="comment.id" class="card mb-3 shadow-sm">
        <div class="card-body">
          <div class="d-flex align-items-start gap-3">
            <a href="#" @click.prevent="changePage('ProfilePage', comment.user_id)" class="text-decoration-none">
              <img
                :src="comment.user && comment.user.avatar ? PUBLIC + comment.user.avatar : '/images/default_avatar.png'"
                class="rounded-circle"
                style="width:40px;height:40px;"
                alt=""
              />
            </a>
            <div class="flex-grow-1">
              <div class="d-flex justify-content-between">
                <a href="#" @click.prevent="changePage('ProfilePage', comment.user_id)" class="text-decoration-none fw-bold text-primary">
                  <b>{{ comment.user ? comment.user.full_name : '' }}</b>
                </a>
                <span class="badge bg-secondary">{{ comment.category ? comment.category.name : '' }}</span>
              </div>
              <p class="mb-3">{{ comment.text }}</p>
            </div>
          </div>
          <div class="d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-outline-secondary btn-sm" @click="showReply(comment)">Ответить</button>
            <a href="#" class="btn btn-outline-secondary btn-sm" @click.prevent="changePage('EditCommentPage', comment.id)">Редактировать</a>
            <button type="button" class="btn btn-outline-secondary btn-sm" @click="commentdel(comment.id)">Удалить</button>
          </div>

          <div v-if="replyId == comment.id" class="mt-3">
            <textarea v-model="replyText" rows="3" class="form-control mb-2"></textarea>
            <button type="button" class="btn btn-primary btn-sm" @click="answer(comment.id)">Сохранить ответ</button>
            <button type="button" class="btn btn-outline-secondary btn-sm" @click="replyId = null">Отмена</button>
          </div>

          <div v-if="comment.replies && comment.replies[0]" class="mt-3 alert alert-light border-start border-primary border-3 p-3">
            <p class="mb-0"><b>Ответ:</b> {{ comment.replies[0].text }}</p>
            <div class="d-flex gap-2 flex-wrap mt-2">
              <button type="button" class="btn btn-outline-secondary btn-sm" @click="showReply(comment)">Редактировать ответ</button>
              <button type="button" class="btn btn-outline-secondary btn-sm" @click="answerdel(comment.replies[0].id)">Удалить ответ</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="tab == 'users'" class="mb-4">
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Пользователь</th>
              <th>Email</th>
              <th>Телефон</th>
              <th>Роль</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in users" :key="user.id" @click="changePage('ProfilePage', user.id)">
              <td>
                <img :src="user.avatar ? PUBLIC + user.avatar : '/images/default_avatar.png'" class="rounded-circle" style="width:24px;height:24px;" alt="" />
                {{ user.full_name || user.login }}
              </td>
              <td>{{ user.email }}</td>
              <td>{{ user.phone }}</td>
              <td>{{ user.role }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminPage',
  props: ['datasend', 'changePage', 'PUBLIC'],
  data() {
    return {
      tab: 'comments',
      comments: [],
      users: [],
      replyId: null,
      replyText: null,
      replyEditId: null,
    };
  },
  mounted() {
    this.loadComments();
    this.loadUsers();
  },
  methods: {
    loadComments() {
      this.datasend('comments')
        .then((result) => {
          console.log(result);
          this.comments = result.data || result;
        });
    },
    loadUsers() {
      this.datasend('users')
        .then((result) => {
          console.log(result);
          this.users = result.users || result;
        });
    },
    commentdel(id) {
      this.datasend('commentdel/' + id, 'DELETE').then(() => this.loadComments());
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
            this.loadComments();
          }
        });
    },
    answerdel(id) {
        this.datasend('answerdel/' + id, 'DELETE').then(() => this.loadComments());
    },
  },
};
</script>
