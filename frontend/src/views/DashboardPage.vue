<template>
  <div class="home">
    <p>Input URL</p>
    <div class="flex">
      <input type="url" v-model="url" placeholder="Input URL" />
      <button @click="submit"> Submit </button>
    </div>
    <span v-if="isNotUrl" class="alert"> The URL is not valid!</span>
    <div class="flex-short">
      <span>Shortner URL is:</span>
      <input type="url" :value="shortnerUrl" :size="shortnerUrl.length"/>
      <button @click="goToShortnerUrl"> Go to shortner URL</button>
    </div>
  </div>
</template>

<script>
// @ is an alias to /src

import axios from 'axios';

export default {
  name: 'Dashboard',

  data: () => ({
    url:'',
    shortnerUrl:'',
    token:'',
    urlPairs:[],
    isNotUrl: false
  }),
  async created(){
    await axios.get('http://127.0.0.1:8000/api')
    .then((response) => {

      this.urlPairs = response.data.urls.reduce((acc,curr)=> (acc[curr.params]=curr.url,acc),{});

      axios.defaults.headers.post['X-CSRF-Token'] = response.data.token;

      if (this.$route.params.url) {

        window.location.href = this.urlPairs[this.$route.params.url];
      }
    })
    .catch((error) =>{
      console.log(error);
    });
  },
  methods: {
    submit() {

      let isValidUrl = this.validateUrl();

      if (isValidUrl) {

        this.isNotUrl = false;

        const data = {
          'url':this.url
        }
        axios.post('http://127.0.0.1:8000/api',data)
        .then((response) =>{

          if (!this.urlPairs.hasOwnProperty(response.data.params)) {

            this.urlPairs[response.data.params] = response.data.url;
          };

          let ownUrl = window.location.href.split('?')[0];
          this.shortnerUrl = ownUrl + response.data.params;

          this.params = response.data.params;
        })
      } else {

        this.isNotUrl = true;
      }
    },
    goToShortnerUrl() {

      this.$router.push(`/${this.params}`)
    },

    validateUrl() {
      return /^(?:(?:(?:https?|ftp):)?\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:[/?#]\S*)?$/i.test(this.url);
    }
  },
  watch: {
    '$route.params'() {

      if (this.$route.params.url) {

        window.location.href = this.urlPairs[this.$route.params.url];
      }
    }
  }
};
</script>
<style>

.flex {
  display: flex;
}

.flex-short {
  display: flex;
  margin-top: 1rem;
}

.home {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-top: 5rem;
}

.alert {
  color:red;
  font-size: 0.75rem;
  margin-top: 0.5rem;
}

</style>