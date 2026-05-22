<template>
  <div class="container py-4">
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-6">
        <div class="card shadow">
          <div class="card-body">
            <h2 class="mb-4">Редактировать отзыв</h2>

            <textarea v-model="text" rows="6" class="form-control mb-3"></textarea>
            <div class="invalid-feedback d-block" v-if="errors.text">{{ errors.text.join('. ') }}</div>

            <div v-if="photos.length > 0" class="mb-3">
              <div class="row g-2">
                <div v-for="photo in photos" :key="photo.id" class="col-4">
                  <img :src="PUBLIC + photo.photo_path" class="img-fluid rounded" />
                </div>
              </div>
            </div>

            <div class="mb-3">
              <label for="edit-photos" class="form-label">Новые фото (макс. 3)</label>
              <input type="file" id="edit-photos" multiple accept="image/*" class="form-control" />
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
              <button type="button" class="btn btn-secondary me-md-2" @click="changePage('HomePage')">Отмена</button>
              <button type="button" class="btn btn-primary" @click="commentedit">Сохранить</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'EditCommentPage',
  props: ['pageId', 'changePage', 'datasend', 'PUBLIC'],
  data() {
    return {
      text: null,
      photos: [],
      errors: {},
    };
  },
  mounted() {
    this.datasend('comment/' + this.pageId)
      .then((result) => {
        console.log(result);
        this.text = result.text;
        this.photos = result.photos || [];
      });
  },
  methods: {
    commentedit() {
      this.errors = {};
      let formdata = new FormData();
      if (this.text) formdata.append('text', this.text);

      let files = document.querySelector('#edit-photos');
      if (files && files.files[0]) formdata.append('photo', files.files[0]);
      if (files && files.files[1]) formdata.append('photo2', files.files[1]);
      if (files && files.files[2]) formdata.append('photo3', files.files[2]);

      this.datasend('commentedit/' + this.pageId, 'POST', formdata)
        .then((result) => {
          console.log(result);
          if (result.errors) {
            this.errors = result.errors;
          }
          if (result.message == 'ok') {
            this.changePage('HomePage');
          }
        });
    },
  },
};
</script>
