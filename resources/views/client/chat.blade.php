@extends('layouts.app-client')
@section('title', 'Chats')
@push('styles')
<style>
  .chat {
    list-style: none;
    margin: 0;
    padding: 0;
  }

  .chat li {
    margin-bottom: 10px;
    padding-bottom: 5px;
    border-bottom: 1px dotted #B3A9A9;
  }

  .chat li .chat-body p {
    margin: 0;
    color: #777777;
  }

  .panel-body {
    overflow-y: scroll;
    height: 350px;
  }

  ::-webkit-scrollbar-track {
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
    background-color: #F5F5F5;
  }

  ::-webkit-scrollbar {
    width: 12px;
    background-color: #F5F5F5;
  }

  ::-webkit-scrollbar-thumb {
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
    background-color: #555;
  }
</style>
@endpush
@section('content')
<!-- 0 for user -->
<!-- 1 for sale agent -->
<input type="hidden" id="check_chat" value="0">
<div id="app">
  <div class="container">
      <div class="row">
          <div class="col-md-8 col-md-offset-2">
              <div class="panel panel-default">
                  <div class="panel-heading">Chats</div>

                  <div class="panel-body">
                      <chat-messages :messages="messages"></chat-messages>
                  </div>
                  <div class="panel-footer">
                      <chat-form
                          v-on:messagesent="addMessage"
                          :user="{{ Auth::user() }}"
                      ></chat-form>
                  </div>
              </div>
          </div>
      </div>
  </div>
</div>
@endsection
@push('scripts')

@endpush