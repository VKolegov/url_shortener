<script setup>
import { onMounted, ref, watch } from 'vue';
import { useRouter } from 'vue-router';

import shortUrlsAPI from '@/services/short_urls.js';
import { copyToClipboard } from '@/pages/utils.js';

const router = useRouter();

const props = defineProps({
  id: {
    type: Number,
  }
});

/**
 *
 * @type {import('vue').Ref<ShortUrl | ShortUrlCreateData | ShortUrlUpdateData>}
 */
const shortUrl = ref({});

/**
 *
 * @param id
 */
async function fetch (id) {
  try {
    shortUrl.value = await shortUrlsAPI.fetchOne(id);
  } catch (e) {
    console.error(e);
  }
}

onMounted(() => {
  if (props.id) {
    fetch(props.id);
  }
});

function onSubmit () {
  if (props.id) {
    update();
  } else {
    create();
  }
}

async function update () {
  try {
    shortUrl.value = await shortUrlsAPI.update(props.id, shortUrl.value);
  } catch (e) {
    console.error(e);
  }
}

async function create () {
  try {
    shortUrl.value = await shortUrlsAPI.create(shortUrl.value);
    await router.push({
      name: 'edit',
      params: {
        id: shortUrl.value.id,
      }
    });
  } catch (e) {
    console.error(e);
  }
}

async function copyLinkToClipboard () {
  await copyToClipboard(shortUrl.value.url);
  alert('Скопировано: ' + shortUrl.value.url);
}

/*
routing
 */

watch(() => router.currentRoute.value.params.id, newId => {
  if (!newId) {
    shortUrl.value = {};
  }
});

/*
slug generation
 */

const slugLength = ref(3);

async function generateSlug () {
  try {
    shortUrl.value.slug = await shortUrlsAPI.getFreeSlug(slugLength.value);
  } catch (e) {
    console.error(e);
  }
}
</script>

<template>
  <form
      @submit.prevent="onSubmit"
  >

    <div
        v-if="shortUrl.id"
        class="mb-3"
    >
      <label
          for="url"
          class="form-label"
      >
        Короткая ссылка
      </label>
      <div class="input-group">
        <input
            disabled
            type="url"
            class="form-control"
            id="url"
            :value="shortUrl.url"
        >
        <button
            type="button"
            class="btn btn-primary"
            @click="copyLinkToClipboard"
        >
          Скопировать
        </button>
      </div>
    </div>

    <div
        v-if="!shortUrl.id"
        class="mb-3"
    >
      <label
          for="slug"
          class="form-label"
      >
        Короткий ключ
      </label>
      <div class="input-group">
        <input
            type="text"
            class="form-control"
            id="slug"
            v-model="shortUrl.slug"
            minlength="1"
            maxlength="16"
        >
        <button
            type="button"
            class="btn btn-outline-secondary"
            @click="generateSlug"
        >
          🎲
        </button>
        <span
            class="input-group-text"
        >
          Длина:
        </span>
        <input
            type="number"
            class="form-control length-input"
            v-model="slugLength"
            min="3"
            max="16"
        >
      </div>
    </div>


    <div class="mb-3">
      <label
          for="name"
          class="form-label"
      >
        Название
      </label>
      <input
          type="text"
          class="form-control"
          id="name"
          v-model="shortUrl.name"
      >
    </div>

    <div class="mb-3">
      <label
          for="url"
          class="form-label"
      >
        Ссылка
      </label>
      <input
          type="url"
          class="form-control"
          id="url"
          v-model="shortUrl.destination_url"
          required
      >
    </div>

    <button type="submit" class="btn btn-primary">
      Сохранить
    </button>
  </form>
</template>

<style scoped>
.length-input {
  max-width: 64px;
}
</style>