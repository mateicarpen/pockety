@extends('layouts.app')

@section('content')
    <div class="container" id="app">
        <div class="loading" v-show="loading">
            <img src="/images/loading.gif"/>
            Loading...
        </div>

        <div v-else="!loading">
            <div align="center" v-if="articles.length">
                @{{ currentIndex + 1 }} / @{{ articles.length }} articles
            </div>

            <div class="row" v-show="articles.length">
                <div class="col-xs-1 hidden-xs">
                    <a @click="archiveArticle()" class="prev-next-button">
                    <img src="/images/archive.png"/>
                    archive
                    </a>
                </div>
                <div class="col-xs-12 col-sm-10 article-container">
                    <div class="row">
                        <div class="col-sm-9 col-xs-12">
                            <div class="item-title">
                                <a href="@{{ currentArticle.url }}" target="_blank" class="item-title">
                                    @{{ currentArticle.title }}
                                </a>
                            </div>
                            <div class="item-excerpt">@{{ currentArticle.excerpt }}</div>
                            <div class="item-added">Added on @{{ currentArticle.created_on }}</div>
                        </div>
                        <div class="col-sm-3">
                            <img :src="currentArticle.image" style="max-width: 100%"/>
                        </div>
                    </div>
                </div>
                <div class="col-xs-1 hidden-xs">
                    <a @click="passArticle()" class="prev-next-button">
                    <img src="/images/pass.png"/>
                    pass
                    </a>
                </div>
            </div>

            <div align="center" v-show="!articles.length">
                All Done!
            </div>

            <div class="row" v-show="articles.length">
                <div class="col-xs-12">
                    <div class="tag-container">
                        <div class="tags">
                            <a v-for="tag in tags" @click="addTag(tag)" class="tag">
                            @{{ tag.name }}
                            <span @click.stop="deleteTag(tag)">X</span>
                            </a>
                        </div>
                        <form name="create-tag-form" @submit.prevent="createTag()" style="text-align: center">
                            <input type="text" name="tag" placeholder="Add new tag" v-model="newTag.name" class="new-tag-field"/>
                            <input type="submit" class="btn btn-success" value="Add new tag"/>
                        </form>
                    </div>
                </div>
            </div>

            <a @click="reset()" class="btn btn-danger restart-button">Restart from the oldest article</a>
        </div>
    </div>
@endsection
