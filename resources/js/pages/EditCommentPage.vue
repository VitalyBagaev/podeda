<template>
  <div class="container py-8">
    <div class="card card-accent max-w-2xl mx-auto">
      <h2 class="mb-6">Редактировать отзыв</h2>
      
      <div v-if="loading" class="text-center py-8">Загрузка...</div>
      
      <form v-else action="?" method="post">
        <div class="form-group">
          <label>Текст сообщения</label>
          <textarea v-model="text" rows="6" placeholder="Опишите ваше предложение или проблему..."></textarea>
          <div class="alert alert-danger" v-if="errors && errors.text">
            {{ errors.text.join('. ') }}
          </div>
        </div>

        <div class="form-group">
          <label>Текущие фото</label>
          <div v-if="existingPhotos.length > 0" class="comment-photos mb-4">
            <img v-for="photo in existingPhotos" :key="photo.id" :src="'/' + photo.photo_path" class="comment-photo" />
          </div>
          <p v-else class="text-muted mb-4">Фото отсутствуют</p>
          
          <label>Заменить фото (макс. 3)</label>
          <input type="file" id="edit-photos" multiple accept="image/*" class="mb-2">
          <p class="text-muted" style="font-size: 0.8rem;">При выборе новых файлов старые фото будут удалены.</p>
        </div>
        
        <div class="flex-align-center gap-4 mt-6">
          <button type="button" class="btn btn-primary" @click="updateComment">
            Сохранить изменения
          </button>
          <button type="button" class="btn btn-secondary" @click="changePage('HomePage')">
            Отмена
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  name: 'EditCommentPage',
  props: ['pageId', 'changePage', 'datasend'],
  data() {
    return {
      text: '',
      existingPhotos: [],
      loading: false,
      errors: null
    };
  },
  mounted() {
    this.loadComment();
  },
  methods: {
    loadComment() {
      this.loading = true;
      this.datasend(`comment/${this.pageId}`)
        .then(res => {
          this.text = res.text || '';
          this.existingPhotos = res.photos || [];
          this.loading = false;
        });
    },
    updateComment() {
      this.errors = null;
      let fd = new FormData();
      if (this.text) fd.append('text', this.text);

      let photos = document.querySelector('#edit-photos');
      if (photos && photos.files && photos.files.length > 0) {
        for (let i = 0; i < Math.min(photos.files.length, 3); i++) {
          let fieldName = i === 0 ? 'photo' : (i === 1 ? 'photo2' : 'photo3');
          fd.append(fieldName, photos.files[i]);
        }
      }

      this.datasend(`userCommEdit/${this.pageId}`, 'POST', fd)
        .then(res => {
          if (res.errors) {
            this.errors = res.errors;
          } else {
            alert('Отзыв обновлен!');
            this.changePage('HomePage');
          }
        });
    }
  }
}
</script>
