<template>
  <div>
    <button
      type="button"
      class="btn m-0 p-1 shadow-none"
    >
      <i class="fas fa-heart mr-1"
         :class="{'red-text':this.isLikedBy, 'animated heartBeat fast':this.gotToLike}"
         @click="clickLike"
      />
    </button>
    {{ countLikes }}
  </div>
</template>

<script>
export default {
    props: {
        // ログインユーザが記事にいいねしているかの判定結果
        initialIsLikedBy: {
            type: Boolean,
            default: false,
        },
        // 記事についているいいねの合計数
        initialCountLikes: {
            type: Number,
            default: 0,
        },
        // ログインしているか判定結果
        authorized: {
            type: Boolean,
            default: false,
        },
        // いいね処理のルート
        endpoint: {
            type: String,
        }
    },
    data() {
        return {
            isLikedBy: this.initialIsLikedBy,
            countLikes: this.initialCountLikes,
            gotToLike: false,
        }
    },
    methods: {
        clickLike() {
            if (!this.authorized) {
                alert('いいね機能はログイン中のみ使用できます。');
                return;
            }
            this.isLikedBy ? this.unlike() : this.like();
        },
        // いいね
        async like() {
            const response = await axios.put(this.endpoint);
            this.isLikedBy = true;
            this.countLikes = response.data.countLikes;
            this.gotToLike = true;
        },
        // いいね解除
        async unlike() {
            const response = await axios.delete(this.endpoint);
            this.isLikedBy = false;
            this.countLikes = response.data.countLikes;
            this.gotToLike = false;
        }
    }
}
</script>
