# Vision

## Problem

Organizations often keep their member, payment and scheduling data in a web application, while operational communication is performed manually from a phone.

A typical workflow requires an administrator to:

1. find the relevant users in the application;
2. identify those who require a reminder;
3. search for each contact in WhatsApp;
4. copy and paste the same message;
5. remember which contacts have already been processed.

This workflow is slow, repetitive and error-prone.

## Product vision

CakePHP Communication Center provides one interface for:

- selecting recipients;
- preparing personalized messages;
- choosing a communication channel;
- following campaign progress;
- preserving a delivery history.

The plugin remains independent from the host application's business model.

## Core promise

> Communicate with the right people, at the right time, from one interface.

## First practical objective

For host application, the first usable release must allow an administrator, from a smartphone, to:

- select a recycling center;
- display members whose monthly contribution is unpaid;
- select all or some of them;
- edit a predefined reminder;
- open WhatsApp for each selected recipient with the message already filled in;
- mark each recipient as processed before moving to the next one.

## Long-term objective

The same core must support:

- WhatsApp;
- email;
- SMS;
- internal notifications;
- additional channels through drivers;
- multiple CakePHP applications through recipient providers.
